@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="dashboard-section pt-80">
        <div class="container">
            <div class="pb-80">
                <div class="message__chatbox bg--section">
                    <div class="message__chatbox__header">
                        <h6 class="title">
                            @php echo $myTicket->statusBadge; @endphp
                            @lang('Ticket Id') : <span class="text--base">[#{{ $myTicket->ticket }}]
                                {{ $myTicket->subject }}</span>
                        </h6>
                    </div>
                    <div class="message__chatbox__body">
                        @if ($myTicket->TICKET_CLOSE != 4)
                            <form class="message__chatbox__form row" method="post" action="{{ route('ticket.reply', $myTicket->id) }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="replayTicket" value="1">
                                <div class="form--group col-sm-12">
                                    <textarea class="form-control form--control" name="message" placeholder="@lang('Enter Message')" required=""></textarea>
                                </div>
                                <div class="col-md-9">
                                    <button type="button" class="btn btn-dark btn-sm addAttachment my-2">
                                        <i class="fas fa-plus"></i>
                                         @lang('Add Attachment')
                                    </button>
                                    <p class="mb-2">
                                        <span class="text--info">
                                            @lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')
                                        </span>
                                    </p>
                                    <div class="row fileUploadsContainer gy-2"></div>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn--base w-100 my-2" type="submit"><i
                                            class="la la-fw la-lg la-reply"></i> @lang('Reply')
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="pb-80">
                <div class="message__chatbox bg--section">
                    <div class="message__chatbox__body">
                        <ul class="reply-message-area">
                            @foreach ($messages as $message)
                                <li>
                                    @if ($message->admin_id == 0)
                                        <div class="reply-item ms-auto">
                                            <div class="name-area">
                                                <h6 class="title">{{ __($message->ticket->name) }}</h6>
                                            </div>
                                            <div class="content-area">
                                                <span class="meta-date">
                                                    @lang('Posted on') <span
                                                        class="cl-theme">{{ $message->created_at->format('l, dS F Y @ H:i') }}</span>
                                                </span>
                                                <p>{{ __($message->message) }}</p>
                                                @if ($message->attachments()->count() > 0)
                                                    <div class="mt-2">
                                                        @foreach ($message->attachments as $k => $image)
                                                            <a href="{{ route('ticket.download', encrypt($image->id)) }}"
                                                                class="me-3">
                                                                <i class="fa fa-file"></i> @lang('Attachment')
                                                                {{ ++$k }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="reply-item">
                                            <div class="name-area">
                                                <h6 class="title">{{ __($message->admin->name) }}</h6>
                                            </div>
                                            <div class="content-area">
                                                <span class="meta-date">
                                                    @lang('Posted on'), <span
                                                        class="cl-theme">{{ $message->created_at->format('l, dS F Y @ H:i') }}</span>
                                                </span>
                                                <p>{{ __($message->message) }}</p>
                                                @if ($message->attachments()->count() > 0)
                                                    <div class="mt-2">
                                                        @foreach ($message->attachments as $k => $image)
                                                            <a href="{{ route('ticket.download', encrypt($image->id)) }}"
                                                                class="me-3">
                                                                <i class="fa fa-file"></i>
                                                                @lang('Attachment') {{ ++$k }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                </li>
                            @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addAttachment').on('click',function(){
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled',true)
                }
                $(".fileUploadsContainer").append(`
                    <div class="col-lg-4 col-md-12 removeFileInput">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="file" name="attachments[]" class="form-control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile bg--danger border--danger text-white"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                `)
            });
            $(document).on('click','.removeFile',function(){
                $('.addAttachment').removeAttr('disabled',true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);
    </script>
@endpush
@push('style')
    <style>
        .reply-item {
            width: 90%;
        }
    </style>
@endpush
