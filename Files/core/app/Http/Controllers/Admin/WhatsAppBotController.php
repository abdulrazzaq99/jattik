<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use App\Models\WhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppBotController extends Controller
{
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Show all WhatsApp messages
     */
    public function messages()
    {
        $messages = WhatsAppMessage::with('customer')
            ->latest()
            ->paginate(50);

        $pageTitle = 'WhatsApp Messages';
        return view('admin.whatsapp.messages', compact('pageTitle', 'messages'));
    }

    /**
     * Show active conversations
     */
    public function conversations()
    {
        $conversations = WhatsAppMessage::select('phone_number')
            ->selectRaw('MAX(created_at) as last_message_at')
            ->selectRaw('COUNT(*) as message_count')
            ->groupBy('phone_number')
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        foreach ($conversations as $conversation) {
            $conversation->messages = WhatsAppMessage::where('phone_number', $conversation->phone_number)
                ->with('customer')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        $pageTitle = 'WhatsApp Conversations';
        return view('admin.whatsapp.conversations', compact('pageTitle', 'conversations'));
    }

    /**
     * Show escalated conversations
     */
    public function escalated()
    {
        $escalated = $this->whatsappService->getEscalatedConversations();

        $pageTitle = 'Escalated Conversations';
        return view('admin.whatsapp.escalated', compact('pageTitle', 'escalated'));
    }

    /**
     * Respond to a conversation
     */
    public function respond(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        try {
            $this->whatsappService->sendMessage($validated['phone_number'], $validated['message'], [
                'type' => 'admin_response',
            ]);

            return back()->with('success', 'Message sent successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send message: ' . $e->getMessage());
        }
    }

    /**
     * WhatsApp webhook handler
     */
    public function webhook(Request $request)
    {
        Log::info('WhatsApp webhook received', $request->all());

        // Verify webhook (GET request)
        if ($request->isMethod('get')) {
            $mode = $request->input('hub_mode');
            $token = $request->input('hub_verify_token');
            $challenge = $request->input('hub_challenge');

            $verifyToken = config('services.whatsapp.verify_token', 'your_verify_token');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            }

            return response('Forbidden', 403);
        }

        // Handle incoming message (POST request)
        try {
            $this->whatsappService->handleIncomingMessage($request->all());

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('WhatsApp webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show bot settings
     */
    public function settings()
    {
        $isConfigured = !empty(config('services.whatsapp.access_token'));

        $stats = [
            'total_messages' => WhatsAppMessage::count(),
            'outbound_messages' => WhatsAppMessage::outbound()->count(),
            'inbound_messages' => WhatsAppMessage::inbound()->count(),
            'escalated_conversations' => WhatsAppMessage::escalated()->count(),
            'otps_sent' => WhatsAppMessage::where('message_type', WhatsAppMessage::TYPE_OTP)->count(),
        ];

        $pageTitle = 'WhatsApp Bot Settings';
        return view('admin.whatsapp.settings', compact('pageTitle', 'isConfigured', 'stats'));
    }

    /**
     * View conversation history
     */
    public function conversation($phoneNumber)
    {
        $messages = $this->whatsappService->getConversationHistory($phoneNumber);

        $customer = \App\Models\Customer::where('mobile', $phoneNumber)->first();

        $pageTitle = 'Conversation with ' . $phoneNumber;
        return view('admin.whatsapp.conversation', compact('pageTitle', 'messages', 'customer', 'phoneNumber'));
    }
}
