@extends('dashboard.layout.app')
@section('title', 'Admin Home - Edit Export Formate')
@section('content')
    <h2 class="page-title">Wright Formate</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form method="post" action="{{ route('accounts.export-formate-settings.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <ul class="nav nav-tabs" id="styleTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="h1-tab" data-toggle="tab" href="#h1"
                                    role="tab">Heading 1</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="h2-tab" data-toggle="tab" href="#h2" role="tab">Heading
                                    2</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="h3-tab" data-toggle="tab" href="#h3" role="tab">Heading
                                    3</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="subtitle-tab" data-toggle="tab" href="#subtitle"
                                    role="tab">Subtitle</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="body-tab" data-toggle="tab" href="#body" role="tab">Body</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="footnote-tab" data-toggle="tab" href="#footnote"
                                    role="tab">Footnote</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="figure-tab" data-toggle="tab" href="#figure"
                                    role="tab">Caption</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-3" id="styleTabsContent">
                            <!-- Heading 1 -->
                            <div class="tab-pane fade show active" id="h1" role="tabpanel">
                                <h5>Standard Styles</h5>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Font Name</label>
                                            <select class="form-control" name="h1[standard][name]">
                                                <option value="Arial" selected>Arial</option>
                                                <option value="Times New Roman"
                                                    @if ($formate_values && $formate_values['h1']['standard']['name'] == 'Times New Roman') selected @endif>Times New Roman
                                                </option>
                                                <option value="Calibri" @if ($formate_values && $formate_values['h1']['standard']['name'] == 'Calibri') selected @endif>
                                                    Calibri</option>
                                                <option value="Courier New"
                                                    @if ($formate_values && $formate_values['h1']['standard']['name'] == 'Courier New') selected @endif>Courier New</option>
                                                <option value="Verdana" @if ($formate_values && $formate_values['h1']['standard']['name'] == 'Verdana') selected @endif>
                                                    Verdana</option>
                                                <option value="Tahoma" @if ($formate_values && $formate_values['h1']['standard']['name'] == 'Tahoma') selected @endif>
                                                    Tahoma</option>
                                                <option value="Georgia" @if ($formate_values && $formate_values['h1']['standard']['name'] == 'Georgia') selected @endif>
                                                    Georgia</option>
                                                <option value="Trebuchet MS"
                                                    @if ($formate_values && $formate_values['h1']['standard']['name'] == 'Trebuchet MS') selected @endif>Trebuchet MS</option>
                                                <option value="Impact" @if ($formate_values && $formate_values['h1']['standard']['name'] == 'Impact') selected @endif>
                                                    Impact</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Alignment</label>
                                            <select class="form-control" name="h1[standard][alignment]">
                                                <option value="left" selected>Left</option>
                                                <option value="center"@if ($formate_values && $formate_values['h1']['standard']['alignment'] == 'center') selected @endif>
                                                    Center</option>
                                                <option value="right"@if ($formate_values && $formate_values['h1']['standard']['alignment'] == 'right') selected @endif>
                                                    Right</option>
                                                <option value="justify"@if ($formate_values && $formate_values['h1']['standard']['alignment'] == 'justify') selected @endif>
                                                    Justify</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding: 0px !important; ">
                                        <div class="form-group">
                                            <label>Font Size</label>
                                            <select class="form-control" name="h1[standard][size]">
                                                @foreach ([8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48, 72] as $size)
                                                    <option value="{{ $size }}"
                                                        {{ $formate_values && $formate_values['h1']['standard']['size'] == $size ? 'selected' : ($size == 24 ? 'selected' : '') }}>
                                                        {{ $size }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-left:15px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h1[standard][bold]" value="0">
                                        <input type="checkbox"
                                            class="custom-control-input"@if ($formate_values && $formate_values['h1']['standard']['bold'] == '1') checked @endif
                                            id="h1Bold" name="h1[standard][bold]"value="1">
                                        <label class="custom-control-label" for="h1Bold">Bold</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h1[standard][italic]" value="0">
                                        <input type="checkbox" class="custom-control-input" id="h1Italic"
                                            name="h1[standard][italic]"value="1"
                                            @if ($formate_values && $formate_values['h1']['standard']['italic'] == '1') checked @endif>
                                        <label class="custom-control-label" for="h1Italic">Italic</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h1[standard][underline]" value="0">
                                        <input type="checkbox" class="custom-control-input" id="h1Underline"
                                            name="h1[standard][underline]"value="1"
                                            @if ($formate_values && $formate_values['h1']['standard']['underline'] == '1') checked @endif>
                                        <label class="custom-control-label" for="h1Underline">Underline</label>
                                    </div>
                                </div>


                                <h5 class="mt-4">Paragraph Styles</h5>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Space Before</label>
                                            <input type="number" class="form-control" name="h1[paragraph][spaceBefore]"
                                                value="{{ $formate_values ? $formate_values['h1']['paragraph']['spaceBefore'] : 0 }}"
                                                oninput="this.value = Math.max(0, this.value)">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Space After</label>
                                            <input type="number" class="form-control" name="h1[paragraph][spaceAfter]"
                                                value="{{ $formate_values ? $formate_values['h1']['paragraph']['spaceAfter'] : 12 }}"
                                                oninput="this.value = Math.max(0, this.value)">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding: 0px !important;">
                                        <div class="form-group">
                                            <label>Line Height</label>
                                            <select class="form-control" name="h1[paragraph][lineHeight]">
                                                <option value="1"@if ($formate_values && $formate_values['h1']['paragraph']['lineHeight'] == '1') selected @endif>
                                                    Single (1.0)</option>
                                                <option value="1.15"@if ($formate_values && $formate_values['h1']['paragraph']['lineHeight'] == '1.15') selected @endif>
                                                    1.15</option>
                                                <option value="1.5" selected>1.5</option>
                                                <option value="2"@if ($formate_values && $formate_values['h1']['paragraph']['lineHeight'] == '2') selected @endif>
                                                    Double (2.0)</option>
                                                <option value="2.5"@if ($formate_values && $formate_values['h1']['paragraph']['lineHeight'] == '2.5') selected @endif>
                                                    2.5</option>
                                                <option value="3"@if ($formate_values && $formate_values['h1']['paragraph']['lineHeight'] == '3') selected @endif>
                                                    Triple (3.0)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-6" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Indentation Left</label>
                                            <input type="number" class="form-control"
                                                name="h1[paragraph][indentation][left]" step="0.02"
                                                value="{{ $formate_values ? $formate_values['h1']['paragraph']['indentation']['left'] : 0.56 }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="padding: 0px !important;">
                                        <div class="form-group">
                                            <label>Indentation Hanging</label>
                                            <input type="number" class="form-control"
                                                name="h1[paragraph][indentation][hanging]" step="0.02"
                                                value="{{ $formate_values ? $formate_values['h1']['paragraph']['indentation']['hanging'] : 0.56 }}">
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-left:15px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h1[paragraph][contextualSpacing]" value="0">
                                        <input type="checkbox"
                                            class="custom-control-input"@if ($formate_values && $formate_values['h1']['paragraph']['contextualSpacing'] == '1') checked @endif
                                            id="h1ContextualSpacing" name="h1[paragraph][contextualSpacing]"value="1">
                                        <label class="custom-control-label" for="h1ContextualSpacing">Contextual
                                            Spacing</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h1[paragraph][keepNext]" value="0">
                                        <input type="checkbox" class="custom-control-input" id="h1KeepNext"
                                            name="h1[paragraph][keepNext]"value="1"
                                            @if ($formate_values && $formate_values['h1']['paragraph']['keepNext'] == '1') checked @endif>
                                        <label class="custom-control-label" for="h1KeepNext">Keep With Next</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h1[paragraph][pageBreakBefore]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['h1']['paragraph']['pageBreakBefore'] == '1') checked @endif
                                            id="h1PageBreakBefore" name="h1[paragraph][pageBreakBefore]">
                                        <label class="custom-control-label" for="h1PageBreakBefore">Page Break
                                            Before</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h1[paragraph][widowControl]" value="0">
                                        <input type="checkbox" class="custom-control-input" id="h1WidowControl"
                                            name="h1[paragraph][widowControl]"value="1"
                                            @if ($formate_values && $formate_values['h1']['paragraph']['widowControl'] == '1') checked @endif>
                                        <label class="custom-control-label" for="h1WidowControl">Widow Control</label>
                                    </div>
                                </div>

                            </div>

                            <!-- نفس الشكل يتكرر لـ Heading 2, Heading 3, Subtitle -->
                            <div class="tab-pane fade" id="h2" role="tabpanel">
                                <h5>Standard Styles</h5>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Font Name</label>
                                            <select class="form-control" name="h2[standard][name]">
                                                <option value="Arial" selected>Arial</option>
                                                <option value="Times New Roman"
                                                    @if ($formate_values && $formate_values['h2']['standard']['name'] == 'Times New Roman') selected @endif>Times New Roman
                                                </option>
                                                <option value="Calibri"
                                                    @if ($formate_values && $formate_values['h2']['standard']['name'] == 'Calibri') selected @endif>
                                                    Calibri</option>
                                                <option value="Courier New"
                                                    @if ($formate_values && $formate_values['h2']['standard']['name'] == 'Courier New') selected @endif>Courier New</option>
                                                <option value="Verdana"
                                                    @if ($formate_values && $formate_values['h2']['standard']['name'] == 'Verdana') selected @endif>Verdana</option>
                                                <option value="Tahoma" @if ($formate_values && $formate_values['h2']['standard']['name'] == 'Tahoma') selected @endif>
                                                    Tahoma</option>
                                                <option value="Georgia"
                                                    @if ($formate_values && $formate_values['h2']['standard']['name'] == 'Georgia') selected @endif>Georgia</option>
                                                <option value="Trebuchet MS"
                                                    @if ($formate_values && $formate_values['h2']['standard']['name'] == 'Trebuchet MS') selected @endif>Trebuchet MS
                                                </option>
                                                <option value="Impact" @if ($formate_values && $formate_values['h2']['standard']['name'] == 'Impact') selected @endif>
                                                    Impact</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Alignment</label>
                                            <select class="form-control" name="h2[standard][alignment]">
                                                <option value="left" selected>Left</option>
                                                <option value="center"@if ($formate_values && $formate_values['h2']['standard']['alignment'] == 'center') selected @endif>
                                                    Center</option>
                                                <option value="right"@if ($formate_values && $formate_values['h2']['standard']['alignment'] == 'right') selected @endif>
                                                    Right</option>
                                                <option value="justify"@if ($formate_values && $formate_values['h2']['standard']['alignment'] == 'justify') selected @endif>
                                                    Justify</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding: 0px !important; ">
                                        <div class="form-group">
                                            <label>Font Size</label>
                                            <select class="form-control" name="h2[standard][size]">
                                                @foreach ([8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48, 72] as $size)
                                                    <option value="{{ $size }}"
                                                        {{ $formate_values && $formate_values['h2']['standard']['size'] == $size ? 'selected' : ($size == 22 ? 'selected' : '') }}>
                                                        {{ $size }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-left:15px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h2[standard][bold]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="h2Bold"value="1"
                                            name="h2[standard][bold]" @if ($formate_values && $formate_values['h2']['standard']['bold'] == '1') checked @endif>
                                        <label class="custom-control-label" for="h2Bold">Bold</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h2[standard][italic]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="h2Italic"value="1"
                                            name="h2[standard][italic]" @if ($formate_values && $formate_values['h2']['standard']['italic'] == '1') checked @endif>
                                        <label class="custom-control-label" for="h2Italic">Italic</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h2[standard][underline]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="h2Underline"value="1"
                                            name="h2[standard][underline]"
                                            @if ($formate_values && $formate_values['h2']['standard']['underline'] == '1') checked @endif>
                                        <label class="custom-control-label" for="h2Underline">Underline</label>
                                    </div>
                                </div>


                                <h5 class="mt-4">Paragraph Styles</h5>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Space Before</label>
                                            <input type="number" class="form-control" name="h2[paragraph][spaceBefore]"
                                                value="{{ $formate_values ? $formate_values['h2']['paragraph']['spaceBefore'] : 0 }}"
                                                oninput="this.value = Math.max(0, this.value)">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Space After</label>
                                            <input type="number" class="form-control" name="h2[paragraph][spaceAfter]"
                                                value="{{ $formate_values ? $formate_values['h2']['paragraph']['spaceAfter'] : 12 }}"
                                                oninput="this.value = Math.max(0, this.value)">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding: 0px !important;">
                                        <div class="form-group">
                                            <label>Line Height</label>
                                            <select class="form-control" name="h2[paragraph][lineHeight]">
                                                <option value="1"@if ($formate_values && $formate_values['h2']['paragraph']['lineHeight'] == '1') selected @endif>
                                                    Single (1.0)</option>
                                                <option value="1.15"@if ($formate_values && $formate_values['h2']['paragraph']['lineHeight'] == '1.15') selected @endif>
                                                    1.15</option>
                                                <option value="1.5" selected>1.5</option>
                                                <option value="2"@if ($formate_values && $formate_values['h2']['paragraph']['lineHeight'] == '2') selected @endif>
                                                    Double (2.0)</option>
                                                <option value="2.5"@if ($formate_values && $formate_values['h2']['paragraph']['lineHeight'] == '2.5') selected @endif>
                                                    2.5</option>
                                                <option value="3"@if ($formate_values && $formate_values['h2']['paragraph']['lineHeight'] == '3') selected @endif>
                                                    Triple (3.0)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-6" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Indentation Left</label>
                                            <input type="number" class="form-control"
                                                name="h2[paragraph][indentation][left]" step="0.02"
                                                value="{{ $formate_values ? $formate_values['h2']['paragraph']['indentation']['left'] : 0.56 }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="padding: 0px !important;">
                                        <div class="form-group">
                                            <label>Indentation Hanging</label>
                                            <input type="number" class="form-control"
                                                name="h2[paragraph][indentation][hanging]" step="0.02"
                                                value="{{ $formate_values ? $formate_values['h2']['paragraph']['indentation']['hanging'] : 0.56 }}">
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-left:15px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h2[paragraph][contextualSpacing]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['h2']['paragraph']['contextualSpacing'] == '1') checked @endif
                                            id="h2ContextualSpacing" name="h2[paragraph][contextualSpacing]">
                                        <label class="custom-control-label" for="h2ContextualSpacing">Contextual
                                            Spacing</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h2[paragraph][keepNext]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['h2']['paragraph']['keepNext'] == '1') checked @endif
                                            id="h2KeepNext" name="h2[paragraph][keepNext]">
                                        <label class="custom-control-label" for="h2KeepNext">Keep With Next</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h2[paragraph][pageBreakBefore]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['h2']['paragraph']['pageBreakBefore'] == '1') checked @endif
                                            id="h2PageBreakBefore" name="h2[paragraph][pageBreakBefore]">
                                        <label class="custom-control-label" for="h2PageBreakBefore">Page Break
                                            Before</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h2[paragraph][widowControl]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['h2']['paragraph']['widowControl'] == '1') checked @endif
                                            id="h2WidowControl" name="h2[paragraph][widowControl]">
                                        <label class="custom-control-label" for="h2WidowControl">Widow Control</label>
                                    </div>

                                </div>
                            </div>
                            <div class="tab-pane fade" id="h3" role="tabpanel">
                                <h5>Standard Styles</h5>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Font Name</label>
                                            <select class="form-control" name="h3[standard][name]">
                                                <option value="Arial" selected>Arial</option>
                                                <option value="Times New Roman"
                                                    @if ($formate_values && $formate_values['h3']['standard']['name'] == 'Times New Roman') selected @endif>Times New Roman
                                                </option>
                                                <option value="Calibri"
                                                    @if ($formate_values && $formate_values['h3']['standard']['name'] == 'Calibri') selected @endif>Calibri</option>
                                                <option value="Courier New"
                                                    @if ($formate_values && $formate_values['h3']['standard']['name'] == 'Courier New') selected @endif>Courier New</option>
                                                <option value="Verdana"
                                                    @if ($formate_values && $formate_values['h3']['standard']['name'] == 'Verdana') selected @endif>Verdana</option>
                                                <option value="Tahoma" @if ($formate_values && $formate_values['h3']['standard']['name'] == 'Tahoma') selected @endif>
                                                    Tahoma</option>
                                                <option value="Georgia"
                                                    @if ($formate_values && $formate_values['h3']['standard']['name'] == 'Georgia') selected @endif>Georgia</option>
                                                <option value="Trebuchet MS"
                                                    @if ($formate_values && $formate_values['h3']['standard']['name'] == 'Trebuchet MS') selected @endif>Trebuchet MS
                                                </option>
                                                <option value="Impact" @if ($formate_values && $formate_values['h3']['standard']['name'] == 'Impact') selected @endif>
                                                    Impact</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Alignment</label>
                                            <select class="form-control" name="h3[standard][alignment]">
                                                <option value="left" selected>Left</option>
                                                <option value="center"@if ($formate_values && $formate_values['h3']['standard']['alignment'] == 'center') selected @endif>
                                                    Center</option>
                                                <option value="right"@if ($formate_values && $formate_values['h3']['standard']['alignment'] == 'right') selected @endif>
                                                    Right</option>
                                                <option value="justify"@if ($formate_values && $formate_values['h3']['standard']['alignment'] == 'justify') selected @endif>
                                                    Justify</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding: 0px !important; ">
                                        <div class="form-group">
                                            <label>Font Size</label>
                                            <select class="form-control" name="h3[standard][size]">
                                                @foreach ([8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48, 72] as $size)
                                                    <option value="{{ $size }}"
                                                        {{ $formate_values && $formate_values['h3']['standard']['size'] == $size ? 'selected' : ($size == 20 ? 'selected' : '') }}>
                                                        {{ $size }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-left:15px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h3[standard][bold]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="h3Bold"value="1"
                                            name="h3[standard][bold]" @if ($formate_values && $formate_values['h3']['standard']['bold'] == '1') checked @endif>
                                        <label class="custom-control-label" for="h3Bold">Bold</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h3[standard][italic]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="h3Italic"value="1"
                                            name="h3[standard][italic]" @if ($formate_values && $formate_values['h3']['standard']['italic'] == '1') checked @endif>
                                        <label class="custom-control-label" for="h3Italic">Italic</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h3[standard][underline]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="h3Underline"value="1"
                                            name="h3[standard][underline]"
                                            @if ($formate_values && $formate_values['h3']['standard']['underline'] == '1') checked @endif>
                                        <label class="custom-control-label" for="h3Underline">Underline</label>
                                    </div>
                                </div>


                                <h5 class="mt-4">Paragraph Styles</h5>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Space Before</label>
                                            <input type="number" class="form-control" name="h3[paragraph][spaceBefore]"
                                                value="{{ $formate_values ? $formate_values['h3']['paragraph']['spaceBefore'] : 0 }}"
                                                oninput="this.value = Math.max(0, this.value)">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Space After</label>
                                            <input type="number" class="form-control" name="h3[paragraph][spaceAfter]"
                                                value="{{ $formate_values ? $formate_values['h3']['paragraph']['spaceAfter'] : 12 }}"
                                                oninput="this.value = Math.max(0, this.value)">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding: 0px !important;">
                                        <div class="form-group">
                                            <label>Line Height</label>
                                            <select class="form-control" name="h3[paragraph][lineHeight]">
                                                <option value="1"@if ($formate_values && $formate_values['h3']['paragraph']['lineHeight'] == '1') selected @endif>
                                                    Single (1.0)</option>
                                                <option value="1.15"@if ($formate_values && $formate_values['h3']['paragraph']['lineHeight'] == '1.15') selected @endif>
                                                    1.15</option>
                                                <option value="1.5" selected>1.5</option>
                                                <option value="2"@if ($formate_values && $formate_values['h3']['paragraph']['lineHeight'] == '2') selected @endif>
                                                    Double (2.0)</option>
                                                <option value="2.5"@if ($formate_values && $formate_values['h3']['paragraph']['lineHeight'] == '2.5') selected @endif>
                                                    2.5</option>
                                                <option value="3"@if ($formate_values && $formate_values['h3']['paragraph']['lineHeight'] == '3') selected @endif>
                                                    Triple (3.0)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-6" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Indentation Left</label>
                                            <input type="number" class="form-control"
                                                name="h3[paragraph][indentation][left]" step="0.02"
                                                value="{{ $formate_values ? $formate_values['h2']['paragraph']['indentation']['left'] : 0.56 }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="padding: 0px !important;">
                                        <div class="form-group">
                                            <label>Indentation Hanging</label>
                                            <input type="number" class="form-control"
                                                name="h3[paragraph][indentation][hanging]" step="0.02"
                                                value="{{ $formate_values ? $formate_values['h2']['paragraph']['indentation']['hanging'] : 0.56 }}">
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-left:15px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h3[paragraph][contextualSpacing]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['h3']['paragraph']['contextualSpacing'] == '1') checked @endif
                                            id="h3ContextualSpacing" name="h3[paragraph][contextualSpacing]">
                                        <label class="custom-control-label" for="h3ContextualSpacing">Contextual
                                            Spacing</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h3[paragraph][keepNext]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['h3']['paragraph']['keepNext'] == '1') checked @endif
                                            id="h3KeepNext" name="h3[paragraph][keepNext]">
                                        <label class="custom-control-label" for="h3KeepNext">Keep With Next</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h3[paragraph][pageBreakBefore]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['h3']['paragraph']['pageBreakBefore'] == '1') checked @endif
                                            id="h3PageBreakBefore" name="h3[paragraph][pageBreakBefore]">
                                        <label class="custom-control-label" for="h3PageBreakBefore">Page Break
                                            Before</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="h3[paragraph][widowControl]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['h3']['paragraph']['widowControl'] == '1') checked @endif
                                            id="h3WidowControl" name="h3[paragraph][widowControl]">
                                        <label class="custom-control-label" for="h3WidowControl">Widow Control</label>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="subtitle" role="tabpanel">
                                <h5>Standard Styles</h5>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Font Name</label>
                                            <select class="form-control" name="subtitle[standard][name]">
                                                <option value="Arial" selected>Arial</option>
                                                <option value="Times New Roman"
                                                    @if ($formate_values && $formate_values['subtitle']['standard']['name'] == 'Times New Roman') selected @endif>Times New Roman
                                                </option>
                                                <option value="Calibri"
                                                    @if ($formate_values && $formate_values['subtitle']['standard']['name'] == 'Calibri') selected @endif>Calibri</option>
                                                <option value="Courier New"
                                                    @if ($formate_values && $formate_values['subtitle']['standard']['name'] == 'Courier New') selected @endif>Courier New</option>
                                                <option value="Verdana"
                                                    @if ($formate_values && $formate_values['subtitle']['standard']['name'] == 'Verdana') selected @endif>Verdana</option>
                                                <option value="Tahoma" @if ($formate_values && $formate_values['subtitle']['standard']['name'] == 'Tahoma') selected @endif>
                                                    Tahoma</option>
                                                <option value="Georgia"
                                                    @if ($formate_values && $formate_values['subtitle']['standard']['name'] == 'Georgia') selected @endif>Georgia</option>
                                                <option value="Trebuchet MS"
                                                    @if ($formate_values && $formate_values['subtitle']['standard']['name'] == 'Trebuchet MS') selected @endif>Trebuchet MS
                                                </option>
                                                <option value="Impact" @if ($formate_values && $formate_values['subtitle']['standard']['name'] == 'Impact') selected @endif>
                                                    Impact</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Alignment</label>
                                            <select class="form-control" name="subtitle[standard][alignment]">
                                                <option value="left" selected>Left</option>
                                                <option value="center"@if ($formate_values && $formate_values['subtitle']['standard']['alignment'] == 'center') selected @endif>
                                                    Center</option>
                                                <option value="right"@if ($formate_values && $formate_values['subtitle']['standard']['alignment'] == 'right') selected @endif>
                                                    Right</option>
                                                <option value="justify"@if ($formate_values && $formate_values['subtitle']['standard']['alignment'] == 'justify') selected @endif>
                                                    Justify</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding: 0px !important; ">
                                        <div class="form-group">
                                            <label>Font Size</label>
                                            <select class="form-control" name="subtitle[standard][size]">
                                                @foreach ([8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48, 72] as $size)
                                                    <option value="{{ $size }}"
                                                        {{ $formate_values && $formate_values['subtitle']['standard']['size'] == $size ? 'selected' : ($size == 14 ? 'selected' : '') }}>
                                                        {{ $size }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-left:15px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="subtitle[standard][bold]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="subtitleBold"
                                            name="subtitle[standard][bold]"value="1"
                                            @if ($formate_values && $formate_values['subtitle']['standard']['bold'] == '1') checked @endif>
                                        <label class="custom-control-label" for="subtitleBold">Bold</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="subtitle[standard][italic]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="subtitleItalic"
                                            name="subtitle[standard][italic]"value="1"
                                            @if ($formate_values && $formate_values['subtitle']['standard']['italic'] == '1') checked @endif>
                                        <label class="custom-control-label" for="subtitleItalic">Italic</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="subtitle[standard][underline]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="subtitleUnderline"
                                            name="subtitle[standard][underline]"value="1"
                                            @if ($formate_values && $formate_values['subtitle']['standard']['underline'] == '1') checked @endif>
                                        <label class="custom-control-label" for="subtitleUnderline">Underline</label>
                                    </div>
                                </div>


                                <h5 class="mt-4">Paragraph Styles</h5>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Space Before</label>
                                            <input type="number" class="form-control"
                                                name="subtitle[paragraph][spaceBefore]"
                                                value="{{ $formate_values ? $formate_values['subtitle']['paragraph']['spaceBefore'] : 0 }}"
                                                oninput="this.value = Math.max(0, this.value)">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Space After</label>
                                            <input type="number" class="form-control"
                                                name="subtitle[paragraph][spaceAfter]"
                                                value="{{ $formate_values ? $formate_values['subtitle']['paragraph']['spaceAfter'] : 12 }}"
                                                oninput="this.value = Math.max(0, this.value)">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding: 0px !important;">
                                        <div class="form-group">
                                            <label>Line Height</label>
                                            <select class="form-control" name="subtitle[paragraph][lineHeight]">
                                                <option value="1"@if ($formate_values && $formate_values['subtitle']['paragraph']['lineHeight'] == '1') selected @endif>
                                                    Single (1.0)</option>
                                                <option value="1.15"@if ($formate_values && $formate_values['subtitle']['paragraph']['lineHeight'] == '1.15') selected @endif>
                                                    1.15</option>
                                                <option value="1.5" selected>1.5</option>
                                                <option value="2"@if ($formate_values && $formate_values['subtitle']['paragraph']['lineHeight'] == '2') selected @endif>
                                                    Double (2.0)</option>
                                                <option value="2.5"@if ($formate_values && $formate_values['subtitle']['paragraph']['lineHeight'] == '2.5') selected @endif>
                                                    2.5</option>
                                                <option value="3"@if ($formate_values && $formate_values['subtitle']['paragraph']['lineHeight'] == '3') selected @endif>
                                                    Triple (3.0)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-6" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Indentation Left</label>
                                            <input type="number" class="form-control"
                                                name="subtitle[paragraph][indentation][left]" step="0.02"
                                                value="{{ $formate_values ? $formate_values['subtitle']['paragraph']['indentation']['left'] : 0.56 }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="padding: 0px !important;">
                                        <div class="form-group">
                                            <label>Indentation Hanging</label>
                                            <input type="number" class="form-control"
                                                name="subtitle[paragraph][indentation][hanging]" step="0.02"
                                                value="{{ $formate_values ? $formate_values['subtitle']['paragraph']['indentation']['hanging'] : 0.56 }}">
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-left:15px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="subtitle[paragraph][contextualSpacing]"
                                            value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['subtitle']['paragraph']['contextualSpacing'] == '1') checked @endif
                                            id="subtitleContextualSpacing" name="subtitle[paragraph][contextualSpacing]">
                                        <label class="custom-control-label" for="subtitleContextualSpacing">Contextual
                                            Spacing</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="subtitle[paragraph][keepNext]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['subtitle']['paragraph']['keepNext'] == '1') checked @endif
                                            id="subtitleKeepNext" name="subtitle[paragraph][keepNext]">
                                        <label class="custom-control-label" for="subtitleKeepNext">Keep With Next</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="subtitle[paragraph][pageBreakBefore]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['subtitle']['paragraph']['pageBreakBefore'] == '1') checked @endif
                                            id="subtitlePageBreakBefore" name="subtitle[paragraph][pageBreakBefore]">
                                        <label class="custom-control-label" for="subtitlePageBreakBefore">Page Break
                                            Before</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="subtitle[paragraph][widowControl]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['subtitle']['paragraph']['widowControl'] == '1') checked @endif
                                            id="subtitleWidowControl" name="subtitle[paragraph][widowControl]">
                                        <label class="custom-control-label" for="subtitleWidowControl">Widow
                                            Control</label>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="body" role="tabpanel">
                                <h5>Standard Styles</h5>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Font Name</label>
                                            <select class="form-control" name="body[standard][name]">
                                                <option value="Arial" selected>Arial</option>
                                                <option value="Times New Roman"
                                                    @if ($formate_values && $formate_values['body']['standard']['name'] == 'Times New Roman') selected @endif>Times New Roman
                                                </option>
                                                <option value="Calibri"
                                                    @if ($formate_values && $formate_values['body']['standard']['name'] == 'Calibri') selected @endif>Calibri</option>
                                                <option value="Courier New"
                                                    @if ($formate_values && $formate_values['body']['standard']['name'] == 'Courier New') selected @endif>Courier New</option>
                                                <option value="Verdana"
                                                    @if ($formate_values && $formate_values['body']['standard']['name'] == 'Verdana') selected @endif>Verdana</option>
                                                <option value="Tahoma" @if ($formate_values && $formate_values['body']['standard']['name'] == 'Tahoma') selected @endif>
                                                    Tahoma</option>
                                                <option value="Georgia"
                                                    @if ($formate_values && $formate_values['body']['standard']['name'] == 'Georgia') selected @endif>Georgia</option>
                                                <option value="Trebuchet MS"
                                                    @if ($formate_values && $formate_values['body']['standard']['name'] == 'Trebuchet MS') selected @endif>Trebuchet MS
                                                </option>
                                                <option value="Impact" @if ($formate_values && $formate_values['body']['standard']['name'] == 'Impact') selected @endif>
                                                    Impact</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Alignment</label>
                                            <select class="form-control" name="body[standard][alignment]">
                                                <option value="left" selected>Left</option>
                                                <option value="center"@if ($formate_values && $formate_values['body']['standard']['alignment'] == 'center') selected @endif>
                                                    Center</option>
                                                <option value="right"@if ($formate_values && $formate_values['body']['standard']['alignment'] == 'right') selected @endif>
                                                    Right</option>
                                                <option value="justify"@if ($formate_values && $formate_values['body']['standard']['alignment'] == 'justify') selected @endif>
                                                    Justify</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding: 0px !important; ">
                                        <div class="form-group">
                                            <label>Font Size</label>
                                            <select class="form-control" name="body[standard][size]">
                                                @foreach ([8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48, 72] as $size)
                                                    <option value="{{ $size }}"
                                                        {{ $formate_values && $formate_values['body']['standard']['size'] == $size ? 'selected' : ($size == 11 ? 'selected' : '') }}>
                                                        {{ $size }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="mt-4">Paragraph Styles</h5>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Space Before</label>
                                            <input type="number" class="form-control"
                                                name="body[paragraph][spaceBefore]"
                                                value="{{ $formate_values ? $formate_values['body']['paragraph']['spaceBefore'] : 0 }}"
                                                oninput="this.value = Math.max(0, this.value)">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Space After</label>
                                            <input type="number" class="form-control" name="body[paragraph][spaceAfter]"
                                                value="{{ $formate_values ? $formate_values['body']['paragraph']['spaceAfter'] : 12 }}"
                                                oninput="this.value = Math.max(0, this.value)">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding: 0px !important;">
                                        <div class="form-group">
                                            <label>Line Height</label>
                                            <select class="form-control" name="body[paragraph][lineHeight]">
                                                <option value="1"@if ($formate_values && $formate_values['body']['paragraph']['lineHeight'] == '1') selected @endif>
                                                    Single (1.0)</option>
                                                <option
                                                    value="1.15"@if ($formate_values && $formate_values['body']['paragraph']['lineHeight'] == '1.15') selected @endif>
                                                    1.15</option>
                                                <option value="1.5" selected>1.5</option>
                                                <option
                                                    value="2"@if ($formate_values && $formate_values['body']['paragraph']['lineHeight'] == '2') selected @endif>
                                                    Double (2.0)</option>
                                                <option
                                                    value="2.5"@if ($formate_values && $formate_values['body']['paragraph']['lineHeight'] == '2.5') selected @endif>
                                                    2.5</option>
                                                <option
                                                    value="3"@if ($formate_values && $formate_values['body']['paragraph']['lineHeight'] == '3') selected @endif>
                                                    Triple (3.0)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-6" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Indentation Left</label>
                                            <input type="number" class="form-control"
                                                name="body[paragraph][indentation][left]" step="0.02"
                                                value="{{ $formate_values ? $formate_values['body']['paragraph']['indentation']['left'] : 0.56 }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="padding: 0px !important;">
                                        <div class="form-group">
                                            <label>Indentation Hanging</label>
                                            <input type="number" class="form-control"
                                                name="body[paragraph][indentation][hanging]" step="0.02"
                                                value="{{ $formate_values ? $formate_values['body']['paragraph']['indentation']['hanging'] : 0.56 }}">
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-left:15px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="body[paragraph][contextualSpacing]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['body']['paragraph']['contextualSpacing'] == '1') checked @endif
                                            id="bodyContextualSpacing" name="body[paragraph][contextualSpacing]">
                                        <label class="custom-control-label" for="bodyContextualSpacing">Contextual
                                            Spacing</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="body[paragraph][keepNext]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['body']['paragraph']['keepNext'] == '1') checked @endif
                                            id="bodyKeepNext" name="body[paragraph][keepNext]">
                                        <label class="custom-control-label" for="bodyKeepNext">Keep With Next</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="body[paragraph][widowControl]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['body']['paragraph']['widowControl'] == '1') checked @endif
                                            id="bodyWidowControl" name="body[paragraph][widowControl]">
                                        <label class="custom-control-label" for="bodyWidowControl">Widow Control</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="body[paragraph][keepLines]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['body']['paragraph']['keepLines'] == '1') checked @endif
                                            id="bodyKeepLines" name="body[paragraph][keepLines]">
                                        <label class="custom-control-label" for="bodyKeepLines">keep Lines</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="body[paragraph][hyphenation]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['body']['paragraph']['hyphenation'] == '1') checked @endif
                                            id="bodyHyphenation" name="body[paragraph][hyphenation]">
                                        <label class="custom-control-label" for="bodyHyphenation">Hyphenation</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="body[paragraph][pageBreakBefore]" value="0">

                                        <input type="checkbox"value="1"
                                            class="custom-control-input"@if ($formate_values && $formate_values['body']['paragraph']['pageBreakBefore'] == '1') checked @endif
                                            id="bodyPageBreakBefore" name="body[paragraph][pageBreakBefore]">
                                        <label class="custom-control-label" for="bodyPageBreakBefore">Page Break
                                            Before</label>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="footnote" role="tabpanel">
                                <h5>Standard Styles</h5>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Font Name</label>
                                            <select class="form-control" name="footnote[standard][name]">
                                                <option value="Arial" selected>Arial</option>
                                                <option value="Times New Roman"
                                                    @if ($formate_values && $formate_values['footnote']['standard']['name'] == 'Times New Roman') selected @endif>Times New Roman
                                                </option>
                                                <option value="Calibri"
                                                    @if ($formate_values && $formate_values['footnote']['standard']['name'] == 'Calibri') selected @endif>Calibri</option>
                                                <option value="Courier New"
                                                    @if ($formate_values && $formate_values['footnote']['standard']['name'] == 'Courier New') selected @endif>Courier New
                                                </option>
                                                <option value="Verdana"
                                                    @if ($formate_values && $formate_values['footnote']['standard']['name'] == 'Verdana') selected @endif>Verdana</option>
                                                <option value="Tahoma"
                                                    @if ($formate_values && $formate_values['footnote']['standard']['name'] == 'Tahoma') selected @endif>
                                                    Tahoma</option>
                                                <option value="Georgia"
                                                    @if ($formate_values && $formate_values['footnote']['standard']['name'] == 'Georgia') selected @endif>Georgia</option>
                                                <option value="Trebuchet MS"
                                                    @if ($formate_values && $formate_values['footnote']['standard']['name'] == 'Trebuchet MS') selected @endif>Trebuchet MS
                                                </option>
                                                <option value="Impact"
                                                    @if ($formate_values && $formate_values['footnote']['standard']['name'] == 'Impact') selected @endif>
                                                    Impact</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Alignment</label>
                                            <select class="form-control" name="footnote[standard][alignment]">
                                                <option value="left" selected>Left</option>
                                                <option
                                                    value="center"@if ($formate_values && $formate_values['footnote']['standard']['alignment'] == 'center') selected @endif>
                                                    Center</option>
                                                <option
                                                    value="right"@if ($formate_values && $formate_values['footnote']['standard']['alignment'] == 'right') selected @endif>
                                                    Right</option>
                                                <option
                                                    value="justify"@if ($formate_values && $formate_values['footnote']['standard']['alignment'] == 'justify') selected @endif>
                                                    Justify</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding: 0px !important; ">
                                        <div class="form-group">
                                            <label>Font Size</label>
                                            <select class="form-control" name="footnote[standard][size]">
                                                @foreach ([8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48, 72] as $size)
                                                    <option value="{{ $size }}"
                                                        {{ $formate_values && $formate_values['footnote']['standard']['size'] == $size ? 'selected' : ($size == 9 ? 'selected' : '') }}>
                                                        {{ $size }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-left:15px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="footnote[standard][bold]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="footnoteBold"
                                            name="footnote[standard][bold]"value="1"
                                            @if ($formate_values && $formate_values['footnote']['standard']['bold'] == '1') checked @endif>

                                        <label class="custom-control-label" for="footnoteBold">Bold</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="footnote[standard][italic]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="footnoteItalic"
                                            name="footnote[standard][italic]"value="1"
                                            @if ($formate_values && $formate_values['footnote']['standard']['italic'] == '1') checked @endif>

                                        <label class="custom-control-label" for="footnoteItalic">Italic</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="footnote[standard][underline]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="footnoteUnderline"
                                            name="footnote[standard][underline]"value="1"
                                            @if ($formate_values && $formate_values['footnote']['standard']['underline'] == '1') checked @endif>

                                        <label class="custom-control-label" for="footnoteUnderline">Underline</label>
                                    </div>
                                </div>


                                <h5 class="mt-4">Paragraph Styles</h5>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Space Before</label>
                                            <input type="number" class="form-control"
                                                name="footnote[paragraph][spaceBefore]"
                                                value="{{ $formate_values ? $formate_values['footnote']['paragraph']['spaceBefore'] : 0 }}"
                                                oninput="this.value = Math.max(0, this.value)">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Space After</label>
                                            <input type="number" class="form-control"
                                                name="footnote[paragraph][spaceAfter]"
                                                value="{{ $formate_values ? $formate_values['footnote']['paragraph']['spaceAfter'] : 12 }}"
                                                oninput="this.value = Math.max(0, this.value)">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding: 0px !important;">
                                        <div class="form-group">
                                            <label>Line Height</label>
                                            <select class="form-control" name="footnote[paragraph][lineHeight]">
                                                <option
                                                    value="1"@if ($formate_values && $formate_values['footnote']['paragraph']['lineHeight'] == '1') selected @endif>
                                                    Single (1.0)</option>
                                                <option
                                                    value="1.15"@if ($formate_values && $formate_values['footnote']['paragraph']['lineHeight'] == '1.15') selected @endif>
                                                    1.15</option>
                                                <option value="1.5" selected>1.5</option>
                                                <option
                                                    value="2"@if ($formate_values && $formate_values['footnote']['paragraph']['lineHeight'] == '2') selected @endif>
                                                    Double (2.0)</option>
                                                <option
                                                    value="2.5"@if ($formate_values && $formate_values['footnote']['paragraph']['lineHeight'] == '2.5') selected @endif>2.5
                                                </option>
                                                <option
                                                    value="3"@if ($formate_values && $formate_values['footnote']['paragraph']['lineHeight'] == '3') selected @endif>
                                                    Triple (3.0)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-6" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Indentation Left</label>
                                            <input type="number" class="form-control"
                                                name="footnote[paragraph][indentation][left]" step="0.02"
                                                value="{{ $formate_values ? $formate_values['footnote']['paragraph']['indentation']['left'] : 0.56 }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="padding: 0px !important;">
                                        <div class="form-group">
                                            <label>Indentation Hanging</label>
                                            <input type="number" class="form-control"
                                                name="footnote[paragraph][indentation][hanging]" step="0.02"
                                                value="{{ $formate_values ? $formate_values['footnote']['paragraph']['indentation']['hanging'] : 0.56 }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="figure" role="tabpanel">
                                <h5>Standard Styles</h5>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Font Name</label>
                                            <select class="form-control" name="figure[standard][name]">
                                                <option value="Arial" selected>Arial</option>
                                                <option value="Times New Roman"
                                                    @if ($formate_values && $formate_values['figure']['standard']['name'] == 'Times New Roman') selected @endif>Times New Roman
                                                </option>
                                                <option value="Calibri"
                                                    @if ($formate_values && $formate_values['figure']['standard']['name'] == 'Calibri') selected @endif>Calibri</option>
                                                <option value="Courier New"
                                                    @if ($formate_values && $formate_values['figure']['standard']['name'] == 'Courier New') selected @endif>Courier New
                                                </option>
                                                <option value="Verdana"
                                                    @if ($formate_values && $formate_values['figure']['standard']['name'] == 'Verdana') selected @endif>Verdana</option>
                                                <option value="Tahoma"
                                                    @if ($formate_values && $formate_values['figure']['standard']['name'] == 'Tahoma') selected @endif>
                                                    Tahoma</option>
                                                <option value="Georgia"
                                                    @if ($formate_values && $formate_values['figure']['standard']['name'] == 'Georgia') selected @endif>Georgia</option>
                                                <option value="Trebuchet MS"
                                                    @if ($formate_values && $formate_values['figure']['standard']['name'] == 'Trebuchet MS') selected @endif>Trebuchet MS
                                                </option>
                                                <option value="Impact"
                                                    @if ($formate_values && $formate_values['figure']['standard']['name'] == 'Impact') selected @endif>
                                                    Impact</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding-left: 0px !important;">
                                        <div class="form-group">
                                            <label>Alignment</label>
                                            <select class="form-control" name="figure[standard][alignment]">
                                                <option value="left" selected>Left</option>
                                                <option
                                                    value="center"@if ($formate_values && $formate_values['figure']['standard']['alignment'] == 'center') selected @endif>
                                                    Center</option>
                                                <option
                                                    value="right"@if ($formate_values && $formate_values['figure']['standard']['alignment'] == 'right') selected @endif>
                                                    Right</option>
                                                <option
                                                    value="justify"@if ($formate_values && $formate_values['figure']['standard']['alignment'] == 'justify') selected @endif>
                                                    Justify</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding: 0px !important; ">
                                        <div class="form-group">
                                            <label>Font Size</label>
                                            <select class="form-control" name="figure[standard][size]">
                                                @foreach ([8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48, 72] as $size)
                                                    <option value="{{ $size }}"
                                                        {{ $formate_values && $formate_values['figure']['standard']['size'] == $size ? 'selected' : ($size == 9 ? 'selected' : '') }}>
                                                        {{ $size }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-left:15px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="figure[standard][bold]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="figureBold"
                                            name="figure[standard][bold]"value="1"
                                            @if ($formate_values && $formate_values['figure']['standard']['bold'] == '1') checked @endif>

                                        <label class="custom-control-label" for="figureBold">Bold</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="figure[standard][italic]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="figureItalic"
                                            name="figure[standard][italic]"value="1"
                                            @if ($formate_values && $formate_values['figure']['standard']['italic'] == '1') checked @endif>

                                        <label class="custom-control-label" for="figureItalic">Italic</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="figure[standard][underline]" value="0">

                                        <input type="checkbox" class="custom-control-input" id="figureUnderline"
                                            name="figure[standard][underline]"value="1"
                                            @if ($formate_values && $formate_values['figure']['standard']['underline'] == '1') checked @endif>

                                        <label class="custom-control-label" for="figureUnderline">Underline</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn mb-2 btn-outline-primary"id="btn-outline-primary"
                            style="margin-top: 10px;">Save</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
        });
    </script>
@endpush
