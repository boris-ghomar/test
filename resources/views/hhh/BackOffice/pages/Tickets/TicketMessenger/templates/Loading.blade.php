<div id="LoadingTemplate">
    <div class="row chat-message me-5 flex-row-reverse">
        <div class="card" style="background-color: transparent;">
            <div class="card-body" style="margin: 0; padding: 0;">

                <div class="dot-opacity-loader" style="height: auto;">
                    @php
                        $dotStyle = 'width:10px; height:10px; margin: 15px 5px;';
                    @endphp
                    <span style="{{ $dotStyle }}"></span>
                    <span style="{{ $dotStyle }}"></span>
                    <span style="{{ $dotStyle }}"></span>
                </div>

            </div>
        </div>

    </div>
</div>
