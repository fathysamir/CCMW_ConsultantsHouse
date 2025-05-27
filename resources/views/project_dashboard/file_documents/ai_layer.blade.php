@extends('project_dashboard.layout.app')
@section('title', 'Project Home - AI Analyses')
@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5.0.0/dist/tesseract.min.js"></script>
    <style>
        #canvas-container {
            position: relative;
            width: 100%;
            /* Ensure container takes full width */
            overflow: hidden;
            /* Prevent scrollbars */
        }

        #pdf-canvas,
        #selection-canvas {
            border: 1px solid black;
            width: 100% !important;
            /* Force 100% width */
            height: 590PX !important;
            /* Maintain aspect ratio */
            display: block;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            cursor: crosshair;
        }

        .row2 {
            display: flex;
            width: 100%;
        }

        .mr-1 {
            margin-right: 0.25rem;
        }

        .ml-2 {
            margin-left: 0.5rem;
        }

        .date {
            background-color: #fff !important;
        }
    </style>

    @if (session('error'))
        <div id="errorAlert" class="alert alert-danger"
            style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:brown;border-radius: 20px; color:beige;">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div id="successAlert"
            class="alert alert-success"style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:green;border-radius: 20px; color:beige;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row2">
                        <div class="mr-2" style="width:59.4%;">
                            <div style="height: 95.6%;">
                                <textarea style="height: 100%;" class="form-control" id="ocr-result"></textarea>

                            </div>
                            <div style="width: 100%; margin-top:1%;height: 4%;">
                                <button class="btn btn-warning btn-sm" style="width: 100%;color:#fff">Clear</button>
                            </div>
                        </div>
                        <div style="width:40%;">
                            <div id="canvas-container">
                                <canvas id="pdf-canvas"></canvas>
                                <canvas class="overlay" id="selection-canvas"></canvas>
                            </div>
                            <div class="mt-2 d-flex justify-content-between align-items-center">
                                <!-- Left side: Extract button -->
                                <div>
                                    <button onclick="runOCR()" class="btn btn-primary btn-sm">Extract Text</button>
                                </div>

                                <!-- Right side: Equal-width Previous/Next buttons -->
                                <div class="d-flex" style="gap: 8px; min-width: 60px;">
                                    <button onclick="prevPage()" class="btn btn-secondary btn-sm flex-fill"
                                        title="Previous Page">
                                        << </button>
                                            <button onclick="nextPage()" class="btn btn-secondary btn-sm flex-fill"
                                                title="Next Page"> >> </button>
                                </div>
                            </div>
                            <hr>
                            <div class="mt-2 ml-1 mr-1 row justify-content-between align-items-center">
                                <input hidden id="source_id">
                                <input hidden id="ai_zip_file" value="{{ $ai_zip_file }}">
                                <input hidden id="file_doc_id" value="{{ $file_doc_id }}">
                                <input hidden id="ai_pdf_path" value="{{ $ai_pdf_path }}">
                                <h5 style="text-align: center;width:100%;color:rgb(110, 112, 114)">Summarization</h5>
                                <div class="d-flex mb-3" style="height: 20px;width:100%;">
                                    <label style="width: 15%;">Support : </label>
                                    <div class="ml-2 row" style="width: 85%;">
                                        <div class="custom-control custom-radio w-25">
                                            <input type="radio" id="none__g" name="support" value="none"
                                                class="custom-control-input" required checked>
                                            <label class="custom-control-label" for="none__g">None</label>
                                        </div>
                                        <div class="custom-control custom-radio w-25">
                                            <input type="radio" id="Sender" name="support" value="sender"
                                                class="custom-control-input" required>
                                            <label class="custom-control-label" for="Sender">Sender</label>
                                        </div>
                                        <div class="custom-control custom-radio w-25">
                                            <input type="radio" id="Receiver" name="support" value="receiver"
                                                class="custom-control-input" required>
                                            <label class="custom-control-label" for="Receiver">Receiver</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex"style="height: 60px;width:100%; margin-bottom:10px; ">
                                    <label style="width: 15%;">Focus : </label>
                                    <div class="ml-2 row" style="width: 85%;">
                                        <div class="custom-control custom-radio w-25">
                                            <input type="radio" id="None" name="focus" value="none"
                                                class="custom-control-input" required checked>
                                            <label class="custom-control-label" for="None">None</label>
                                        </div>
                                        <div class="custom-control custom-radio w-25">
                                            <input type="radio" id="Note1" name="focus" value="note 1"
                                                class="custom-control-input" required>
                                            <label class="custom-control-label" for="Note1">Note 1</label>
                                        </div>
                                        <div class="custom-control custom-radio w-25">
                                            <input type="radio" id="Note2" name="focus" value="note 2"
                                                class="custom-control-input" required>
                                            <label class="custom-control-label" for="Note2">Note 2</label>
                                        </div>
                                        <div class="custom-control custom-radio w-25">
                                            <input type="radio" id="Narrative" name="focus" value="narrative"
                                                class="custom-control-input" required>
                                            <label class="custom-control-label" for="Narrative">Narrative</label>
                                        </div>
                                        <div class="custom-control custom-radio" style="width: 30%;">
                                            <input type="radio" id="Document_Note" name="focus"
                                                value="document note" class="custom-control-input" required>
                                            <label class="custom-control-label" for="Document_Note">Document Note</label>
                                        </div>
                                        <div class="custom-control custom-radio" style="width: 68%;">
                                            <input type="radio" id="other" name="focus" value="other"
                                                class="custom-control-input" required>
                                            <label class="custom-control-label" style="width: 100%;"
                                                for="other"><input disabled type="text" class="form-control"
                                                    name="other"placeholder="Other" id="otherInput"></label>

                                        </div>


                                    </div>
                                </div>
                                <div style="width: 100%">
                                    <button onclick="Summarize()" class="btn btn-primary btn-sm w-100">Summarize</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="width: 100%; margin-top:20px;">
                        <button class="btn mb-2 btn-danger w-100">Close</button>
                    </div>

                </div> <!-- /.col -->

            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const url = "{{ asset($ai_pdf_path) }}";
        const startPage = 1;


        let currentPage = 0;
        let pdfDoc = null;

        const canvas = document.getElementById('pdf-canvas');
        const ctx = canvas.getContext('2d');

        const selectionCanvas = document.getElementById('selection-canvas');
        const selectionCtx = selectionCanvas.getContext('2d');




        let startX, startY, endX, endY, isDrawing = false;

        pdfjsLib.getDocument(url).promise.then((pdf) => {
            pdfDoc = pdf;
            renderPage(currentPage);
        });

        function renderPage(index) {
            const pageNum = startPage + index;
            if (pageNum > pdfDoc.numPages || pageNum < startPage) return;

            pdfDoc.getPage(pageNum).then(page => {
                const viewport = page.getViewport({
                    scale: 4
                });
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                selectionCanvas.height = viewport.height;
                selectionCanvas.width = viewport.width;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                page.render(renderContext);
                selectionCtx.clearRect(0, 0, selectionCanvas.width, selectionCanvas.height);
            });
        }

        function prevPage() {
            if (currentPage > 0) {
                currentPage--;
                renderPage(currentPage);
            }
        }

        function nextPage() {
            if (currentPage < (pdfDoc.numPages - startPage)) {
                currentPage++;
                renderPage(currentPage);
            }
        }

        selectionCanvas.addEventListener('mousedown', function(e) {
            isDrawing = true;

            const rect = selectionCanvas.getBoundingClientRect();

            const scaleX = selectionCanvas.width / rect.width;
            const scaleY = selectionCanvas.height / rect.height;

            startX = (e.clientX - rect.left) * scaleX;
            startY = (e.clientY - rect.top) * scaleY;
        });

        selectionCanvas.addEventListener('mousemove', function(e) {
            if (!isDrawing) return;

            const rect = selectionCanvas.getBoundingClientRect();

            const scaleX = selectionCanvas.width / rect.width;
            const scaleY = selectionCanvas.height / rect.height;

            endX = (e.clientX - rect.left) * scaleX;
            endY = (e.clientY - rect.top) * scaleY;

            selectionCtx.clearRect(0, 0, selectionCanvas.width, selectionCanvas.height);
            selectionCtx.strokeStyle = "red";
            selectionCtx.lineWidth = 4;
            selectionCtx.strokeRect(startX, startY, endX - startX, endY - startY);
        });

        selectionCanvas.addEventListener('mouseup', function() {
            isDrawing = false;
        });

        function runOCR() {
            const x = Math.min(startX, endX);
            const y = Math.min(startY, endY);
            const width = Math.abs(endX - startX);
            const height = Math.abs(endY - startY);

            const region = ctx.getImageData(x, y, width, height);

            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = width;
            tempCanvas.height = height;
            const tempCtx = tempCanvas.getContext('2d');
            tempCtx.putImageData(region, 0, 0);

            Tesseract.recognize(
                tempCanvas.toDataURL(),
                'eng',
            ).then(({
                data: {
                    text
                }
            }) => {
                const ocrResult = document.getElementById('ocr-result');
                const currentValue = ocrResult.value || '';
                const separator = currentValue ? '\n' : '';
                ocrResult.value = currentValue + separator + text;

            });
        }
    </script>
    <script>
        function Summarize() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let source_id = $('#source_id').val();
            let file_doc_id = $('#file_doc_id').val();
            let ai_zip_file = $('#ai_zip_file').val();
            let ai_pdf_path = $('#ai_pdf_path').val();

            let support = $('input[name="support"]:checked').val();
            let focus = $('input[name="focus"]:checked').val();
            let otherText = $('#otherInput').val();
            if ($('input[name="focus"]:checked').val() === 'other' && otherText.trim() === '') {
                alert('"Other" field is required.');
                return;
            }
            // لو الخيار "other" هو المختار، نستبدل focus بقيمة الكتابة
            if (focus === 'other') {
                focus = otherText;
            }

            $.ajax({
                url: '/project/summarize', // عدّل الرابط حسب ما يناسبك
                type: 'POST',
                data: {

                    source_id: source_id,
                    ai_zip_file: ai_zip_file,
                    ai_pdf_path: ai_pdf_path,
                    support: support,
                    focus: focus,
                    file_doc_id: file_doc_id
                },
                success: function(response) {
                    $('#source_id').val(response.sourceId)
                    const ocrResult = document.getElementById('ocr-result');
                    const currentValue = ocrResult.value || '';
                    const separator = currentValue ? '\n' : '';
                    ocrResult.value = currentValue + separator + response.answer + '\n';
                    //console.log(response);
                    // ممكن تفتح صفحة أو تعمل redirect حسب الحاجة
                },
                error: function(xhr) {
                    alert('حدث خطأ أثناء الإرسال. حاول مرة أخرى.');
                    console.error(xhr);
                }
            });
        }
    </script>
    <script>
        $(document).ready(function() {
            $('input[name="focus"]').on('change', function() {
                if ($(this).val() === 'other' && $(this).is(':checked')) {
                    $('#otherInput').prop('disabled', false);
                } else {
                    $('#otherInput').prop('disabled', true);
                }
            });
            $('.dropdown-toggle').dropdown();
            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
        });
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            flatpickr(".date", {
                enableTime: false,
                dateFormat: "Y-m-d", // Format: YYYY-MM-DD
                altInput: true,
                altFormat: "d.M.Y",
            });

        });
    </script>
@endpush
