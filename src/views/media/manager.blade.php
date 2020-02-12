
<div class="modal fade" id="modal-media" tabindex="-1" role="dialog" aria-labelledby="modal-media" aria-hidden="true">
    <div class="modal-dialog modal-fw modal-dialog-top" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    媒體庫
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group row">
                    <div class="col-lg-8">
                        <div class="form-group">

                            <form id="fileupload" action="{{ route('media.upload') }}" method="POST" enctype="multipart/form-data" class="fileupload-processing">
                                <div class="row fileupload-buttonbar">

                                    <div class="col-12 text-right">
                                        <a class="btn btn-primary btn-block fileinput-button" href="javascript:;">
                                            <i class="fas fa-plus"></i>
                                            <span>上傳</span>
                                            <input type="file" id="file" name="file" style="height:50px;" multiple>
                                        </a>
                                    </div>

                                    <div class="col-12 fileupload-progress p-t-10 fade">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar progress-bar-striped active" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" role="progressbar">
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-4 form-media">
                        <div class="form-group">
                            <div class="input-search">
                                <button type="button" id="btn-search" class="input-search-btn"><i class="icon wb-search" aria-hidden="true"></i></button>
                                <input type="text" class="form-control" id="input-search" placeholder="Search...">
                            </div>
                        </div>
                        <input type="hidden" id="request_time" value="{{ time() }}">
                    </div>
                </div>

                <hr />

                <!-- 媒體庫 infinity scroll -->
                <div class="container-fluid" id="media-container" data-js="scroll-threshold-option">
                    <div class="row" id="media-row"></div>

                    <div class="scroller-status">
                        <div class="loader-ellips infinite-scroll-request">
                            <span class="loader-ellips__dot"></span>
                            <span class="loader-ellips__dot"></span>
                            <span class="loader-ellips__dot"></span>
                            <span class="loader-ellips__dot"></span>
                        </div>
                        <p class="scroller-status__message infinite-scroll-last">End of content</p>
                        <p class="scroller-status__message infinite-scroll-error">No more pages to load</p>
                    </div>
                    
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cons btn-lg btn-default" data-dismiss="modal"><i class="fas fa-times"></i> 取消</button>
                <button type="button" class="btn btn-cons btn-lg btn-primary btn-media-save"><i class="fas fa-upload"></i> 送出</button>
            </div>
        </div>
    </div>
</div>