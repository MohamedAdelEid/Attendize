@extends('Shared.Layouts.Master')

@section('title')
    @parent
    {{ $event->title }} - Ticket Template Settings
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('head')
    <!-- Load jQuery UI and minicolors with specific versions -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/minicolors/2.3.6/jquery.minicolors.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/minicolors/2.3.6/jquery.minicolors.min.css">

    <style>
        /* Reset and base styles */
        .ticket-editor-container * {
            box-sizing: border-box;
        }

        .ticket-preview-container {
            position: relative;
            border: 3px dashed #ddd;
            min-height: 600px;
            background: #f9f9f9;
            overflow: visible;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .template-background {
            position: absolute;
            top: 0;
            left: 0;
            max-width: 100%;
            max-height: 100%;
            z-index: 1;
            object-fit: contain;
        }

        .draggable-element {
            position: absolute !important;
            cursor: move !important;
            z-index: 100 !important;
            padding: 8px !important;
            border: 3px solid !important;
            border-radius: 4px !important;
            background: rgba(255, 255, 255, 0.95) !important;
            font-weight: bold !important;
            user-select: none !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
            min-width: 60px !important;
            min-height: 40px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-family: Arial, sans-serif !important;
        }

        .draggable-element:hover {
            transform: scale(1.1) !important;
            box-shadow: 0 6px 16px rgba(0,0,0,0.4) !important;
        }

        .draggable-element.ui-draggable-dragging {
            transform: rotate(3deg) scale(1.1) !important;
            opacity: 0.9 !important;
            z-index: 1000 !important;
        }

        .element-name {
            border-color: #e74c3c !important;
            color: #e74c3c !important;
        }

        .element-code {
            border-color: #3498db !important;
            color: #3498db !important;
        }

        .element-qr {
            border-color: #27ae60 !important;
            color: #27ae60 !important;
            padding: 4px !important;
        }

        .qr-placeholder {
            background: #ecf0f1 !important;
            border: 2px dashed #95a5a6 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 12px !important;
            color: #7f8c8d !important;
            border-radius: 4px !important;
        }

        .upload-area {
            border: 3px dashed #bdc3c7;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            background: #ecf0f1;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: #3498db;
            background: #e8f4fd;
        }

        .upload-area.dragover {
            border-color: #27ae60;
            background: #d5f4e6;
        }

        .settings-panel {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .coordinate-display {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            margin-top: 10px;
            border: 1px solid #dee2e6;
        }

        .save-button {
            background: linear-gradient(45deg, #3498db, #2980b9);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .save-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .preview-placeholder {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #95a5a6;
            z-index: 0;
        }

        .preview-placeholder i {
            font-size: 64px;
            margin-bottom: 15px;
            display: block;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            border-radius: 8px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Manual positioning controls */
        .position-controls {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .position-input-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .position-input-group label {
            width: 20px;
            margin-right: 5px;
            font-weight: bold;
        }

        .position-input {
            width: 80px;
            margin-right: 10px;
            text-align: center;
        }

        .arrow-controls {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-template-rows: 1fr 1fr 1fr;
            gap: 2px;
            width: 90px;
            height: 90px;
        }

        .arrow-btn {
            background: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            font-size: 14px;
            padding: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .arrow-btn:hover {
            background: #0056b3;
        }

        .arrow-btn:active {
            background: #004085;
        }

        .arrow-up {
            grid-column: 2;
            grid-row: 1;
        }

        .arrow-down {
            grid-column: 2;
            grid-row: 3;
        }

        .arrow-left {
            grid-column: 1;
            grid-row: 2;
        }

        .arrow-right {
            grid-column: 3;
            grid-row: 2;
        }

        .position-controls h5 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #495057;
        }

        /* Override any conflicting styles */
        .ticket-editor-container .form-control {
            display: block;
            width: 100%;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
@stop

@section('content')
<div class="ticket-editor-container">
    <div class="row">
        <div class="col-lg-12">
            <div class="head-title">
                <h3 class="text-primary">
                    <i class="ico-ticket"></i> Ticket Template Designer
                </h3>
                <p class="text-muted">Design your event ticket by uploading a background and positioning elements</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Settings Panel -->
        <div class="col-md-4">
            <div class="settings-panel">
                <h4><i class="ico-upload"></i> Upload Template</h4>

                <div class="upload-area" id="uploadArea">
                    <i class="ico-image" style="font-size: 48px; color: #bdc3c7; margin-bottom: 15px;"></i>
                    <p><strong>Click to upload or drag & drop</strong></p>
                    <p class="text-muted">Supports JPG, PNG (Max: 10MB)</p>
                    <input type="file" id="templateFile" accept=".jpg,.jpeg,.png" style="display: none;">
                    <button type="button" class="btn btn-primary" id="chooseFileBtn">
                        Choose File
                    </button>
                </div>

                @if($template && $template->background_image_path)
                    <div class="alert alert-info" style="margin-top: 15px;">
                        <strong>Current Template:</strong><br>
                        <small>{{ basename($template->background_image_path) }}</small>
                    </div>
                @endif

                <hr>

                <h4><i class="ico-settings"></i> Element Settings</h4>

                <!-- Name Settings -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="panel-title">
                            <span style="color: #e74c3c;">■</span> Attendee Name
                        </h5>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label>Font Size (px)</label>
                            <input type="number" id="nameFontSize" class="form-control"
                                   value="{{ $template->name_font_size ?? 24 }}"
                                   min="8" max="72">
                        </div>
                        <div class="form-group">
                            <label>Font Color</label>
                            <input type="text" id="nameFontColor" class="form-control"
                                   value="{{ $template->name_font_color ?? '#000000' }}">
                        </div>
                        <div class="coordinate-display">
                            Position: <span id="nameCoords">x: {{ $template->name_position_x ?? 50 }}, y: {{ $template->name_position_y ?? 50 }}</span>
                        </div>

                        <!-- Manual Position Controls for Name -->
                        <div class="position-controls">
                            <h5>Manual Position Control</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="position-input-group">
                                        <label>X:</label>
                                        <input type="number" id="namePositionX" class="form-control position-input"
                                               value="{{ $template->name_position_x ?? 50 }}" min="0">
                                    </div>
                                    <div class="position-input-group">
                                        <label>Y:</label>
                                        <input type="number" id="namePositionY" class="form-control position-input"
                                               value="{{ $template->name_position_y ?? 50 }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="arrow-controls">
                                        <button type="button" class="arrow-btn arrow-up" data-element="name" data-direction="up">↑</button>
                                        <button type="button" class="arrow-btn arrow-left" data-element="name" data-direction="left">←</button>
                                        <button type="button" class="arrow-btn arrow-right" data-element="name" data-direction="right">→</button>
                                        <button type="button" class="arrow-btn arrow-down" data-element="name" data-direction="down">↓</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Code Settings -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="panel-title">
                            <span style="color: #3498db;">■</span> Registration Code
                        </h5>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label>Font Size (px)</label>
                            <input type="number" id="codeFontSize" class="form-control"
                                   value="{{ $template->code_font_size ?? 20 }}"
                                   min="8" max="72">
                        </div>
                        <div class="form-group">
                            <label>Font Color</label>
                            <input type="text" id="codeFontColor" class="form-control"
                                   value="{{ $template->code_font_color ?? '#000000' }}">
                        </div>
                        <div class="coordinate-display">
                            Position: <span id="codeCoords">x: {{ $template->code_position_x ?? 50 }}, y: {{ $template->code_position_y ?? 100 }}</span>
                        </div>

                        <!-- Manual Position Controls for Code -->
                        <div class="position-controls">
                            <h5>Manual Position Control</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="position-input-group">
                                        <label>X:</label>
                                        <input type="number" id="codePositionX" class="form-control position-input"
                                               value="{{ $template->code_position_x ?? 50 }}" min="0">
                                    </div>
                                    <div class="position-input-group">
                                        <label>Y:</label>
                                        <input type="number" id="codePositionY" class="form-control position-input"
                                               value="{{ $template->code_position_y ?? 100 }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="arrow-controls">
                                        <button type="button" class="arrow-btn arrow-up" data-element="code" data-direction="up">↑</button>
                                        <button type="button" class="arrow-btn arrow-left" data-element="code" data-direction="left">←</button>
                                        <button type="button" class="arrow-btn arrow-right" data-element="code" data-direction="right">→</button>
                                        <button type="button" class="arrow-btn arrow-down" data-element="code" data-direction="down">↓</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Code Settings -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="panel-title">
                            <span style="color: #27ae60;">■</span> QR Code
                        </h5>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label>Size (px)</label>
                            <input type="number" id="qrSize" class="form-control"
                                   value="{{ $template->qr_size ?? 100 }}"
                                   min="50" max="300">
                        </div>
                        <div class="coordinate-display">
                            Position: <span id="qrCoords">x: {{ $template->qr_position_x ?? 50 }}, y: {{ $template->qr_position_y ?? 150 }}</span>
                        </div>

                        <!-- Manual Position Controls for QR -->
                        <div class="position-controls">
                            <h5>Manual Position Control</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="position-input-group">
                                        <label>X:</label>
                                        <input type="number" id="qrPositionX" class="form-control position-input"
                                               value="{{ $template->qr_position_x ?? 50 }}" min="0">
                                    </div>
                                    <div class="position-input-group">
                                        <label>Y:</label>
                                        <input type="number" id="qrPositionY" class="form-control position-input"
                                               value="{{ $template->qr_position_y ?? 150 }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="arrow-controls">
                                        <button type="button" class="arrow-btn arrow-up" data-element="qr" data-direction="up">↑</button>
                                        <button type="button" class="arrow-btn arrow-left" data-element="qr" data-direction="left">←</button>
                                        <button type="button" class="arrow-btn arrow-right" data-element="qr" data-direction="right">→</button>
                                        <button type="button" class="arrow-btn arrow-down" data-element="qr" data-direction="down">↓</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" id="saveTemplate" class="btn save-button btn-block">
                    <i class="ico-save"></i> Save Template Settings
                </button>
            </div>
        </div>

        <!-- Preview Panel -->
        <div class="col-md-8">
            <div class="settings-panel">
                <h4><i class="ico-eye"></i> Live Preview</h4>
                <p class="text-muted">Drag the elements below to position them on your ticket</p>

                <div id="ticketPreview" class="ticket-preview-container">
                    @if($template && $template->background_image_path)
                        <img src="{{ asset('storage/' . $template->background_image_path) }}"
                             class="template-background"
                             id="templateBackground">
                    @else
                        <div class="preview-placeholder" id="previewPlaceholder">
                            <i class="ico-image"></i>
                            <p>Upload a template to see preview</p>
                        </div>
                    @endif

                    <!-- Draggable Elements -->
                    <div id="nameElement" class="draggable-element element-name"
                         style="left: {{ $template->name_position_x ?? 50 }}px;
                                top: {{ $template->name_position_y ?? 50 }}px;
                                font-size: {{ $template->name_font_size ?? 24 }}px;
                                color: {{ $template->name_font_color ?? '#000000' }};">
                        Full Name
                    </div>

                    <div id="codeElement" class="draggable-element element-code"
                         style="left: {{ $template->code_position_x ?? 50 }}px;
                                top: {{ $template->code_position_y ?? 100 }}px;
                                font-size: {{ $template->code_font_size ?? 20 }}px;
                                color: {{ $template->code_font_color ?? '#000000' }};">
                        ABC123
                    </div>

                    <div id="qrElement" class="draggable-element element-qr"
                         style="left: {{ $template->qr_position_x ?? 50 }}px;
                                top: {{ $template->qr_position_y ?? 150 }}px;">
                        <div class="qr-placeholder" style="width: {{ $template->qr_size ?? 100 }}px;
                                                           height: {{ $template->qr_size ?? 100 }}px;">
                            QR CODE
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <strong>Instructions:</strong>
                    <ul style="margin-bottom: 0;">
                        <li>Upload a background image template</li>
                        <li>Drag the colored elements to position them</li>
                        <li>Use input fields and arrow buttons for precise positioning</li>
                        <li>Adjust font sizes and colors in the settings panel</li>
                        <li>Click "Save Template Settings" to store your design</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('foot')
<script>
// Add this at the beginning of the document ready function
// Wrap the ticket preview container in a form for hidden fields
$('#ticketPreview').wrap('<form id="ticketForm"></form>');

// Wait for everything to load completely
$(window).on('load', function() {
    console.log('Window loaded, initializing ticket editor...');

    // Ensure jQuery UI is available
    if (typeof $.ui === 'undefined') {
        console.error('jQuery UI not loaded!');
        return;
    }

    // Initialize the ticket editor
    initializeTicketEditor();
});

function initializeTicketEditor() {
    console.log('Initializing ticket editor...');

    // Initialize color pickers
    setTimeout(function() {
        initializeColorPickers();
    }, 100);

    // Initialize draggable elements
    setTimeout(function() {
        initializeDraggableElements();
    }, 200);

    // Set up event handlers
    setTimeout(function() {
        setupEventHandlers();
        updateCoordinateDisplays();
    }, 300);

    console.log('Ticket editor initialization complete');
}

function initializeColorPickers() {
    console.log('Initializing color pickers...');

    // Check if minicolors is available
    if (typeof $.fn.minicolors !== 'function') {
        console.warn('Minicolors not available, using regular color inputs');
        $('#nameFontColor, #codeFontColor').attr('type', 'color');
        return;
    }

    try {
        $('#nameFontColor, #codeFontColor').minicolors({
            theme: 'bootstrap',
            format: 'hex',
            opacity: false,
            change: function(value, opacity) {
                console.log('Color changed:', value);
                updateElementStyles();
            }
        });
        console.log('Color pickers initialized');
    } catch (e) {
        console.error('Error initializing color pickers:', e);
        $('#nameFontColor, #codeFontColor').attr('type', 'color');
    }
}

function initializeDraggableElements() {
    console.log('Initializing draggable elements...');

    // Destroy existing draggable instances
    $('.draggable-element').each(function() {
        if ($(this).hasClass('ui-draggable')) {
            $(this).draggable('destroy');
        }
    });

    // Make elements draggable
    $('.draggable-element').draggable({
        containment: '#ticketPreview',
        scroll: false,
        cursor: 'move',
        opacity: 0.9,
        zIndex: 1000,
        start: function(event, ui) {
            console.log('Started dragging:', $(this).attr('id'));
            $(this).addClass('ui-draggable-dragging');
        },
        drag: function(event, ui) {
            updateCoordinateDisplays();
            updatePositionInputs();
        },
        stop: function(event, ui) {
            console.log('Stopped dragging:', $(this).attr('id'), 'Position:', ui.position);
            $(this).removeClass('ui-draggable-dragging');
            updateCoordinateDisplays();
            updatePositionInputs();
        }
    });

    console.log('Draggable elements initialized');
}

function setupEventHandlers() {
    console.log('Setting up event handlers...');

    // File upload button
    $('#chooseFileBtn').off('click').on('click', function() {
        document.getElementById('templateFile').click();
    });

    // File upload handling
    $('#templateFile').off('change').on('change', handleFileUpload);

    // Upload area click
    $('#uploadArea').off('click').on('click', function() {
        $('#templateFile').click();
    });

    // Drag and drop for upload area
    $('#uploadArea').off('dragover dragleave drop').on({
        'dragover': function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        },
        'dragleave': function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        },
        'drop': function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');

            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                $('#templateFile')[0].files = files;
                handleFileUpload();
            }
        }
    });

    // Settings change handlers
    $('#nameFontSize, #codeFontSize, #qrSize').off('input change').on('input change', function() {
        console.log('Size changed:', $(this).attr('id'), $(this).val());
        updateElementStyles();
    });

    $('#nameFontColor, #codeFontColor').off('change input').on('change input', function() {
        console.log('Color changed:', $(this).attr('id'), $(this).val());
        updateElementStyles();
    });

    // Manual position input handlers
    $('#namePositionX, #namePositionY, #codePositionX, #codePositionY, #qrPositionX, #qrPositionY').off('input change').on('input change', function() {
        updateElementPositionsFromInputs();
    });

    // Arrow button handlers
    $('.arrow-btn').off('click').on('click', function() {
        const element = $(this).data('element');
        const direction = $(this).data('direction');
        moveElement(element, direction);
    });

    // Save template
    $('#saveTemplate').off('click').on('click', saveTemplate);

    console.log('Event handlers set up');
}

function updateElementPositionsFromInputs() {
    // Update name element position
    const nameX = parseInt($('#namePositionX').val()) || 0;
    const nameY = parseInt($('#namePositionY').val()) || 0;
    $('#nameElement').css({
        left: nameX + 'px',
        top: nameY + 'px'
    });

    // Update code element position
    const codeX = parseInt($('#codePositionX').val()) || 0;
    const codeY = parseInt($('#codePositionY').val()) || 0;
    $('#codeElement').css({
        left: codeX + 'px',
        top: codeY + 'px'
    });

    // Update QR element position
    const qrX = parseInt($('#qrPositionX').val()) || 0;
    const qrY = parseInt($('#qrPositionY').val()) || 0;
    $('#qrElement').css({
        left: qrX + 'px',
        top: qrY + 'px'
    });

    updateCoordinateDisplays();
}

function updatePositionInputs() {
    // Update position inputs based on element positions
    const namePos = $('#nameElement').position();
    const codePos = $('#codeElement').position();
    const qrPos = $('#qrElement').position();

    if (namePos) {
        $('#namePositionX').val(Math.round(namePos.left));
        $('#namePositionY').val(Math.round(namePos.top));
    }
    if (codePos) {
        $('#codePositionX').val(Math.round(codePos.left));
        $('#codePositionY').val(Math.round(codePos.top));
    }
    if (qrPos) {
        $('#qrPositionX').val(Math.round(qrPos.left));
        $('#qrPositionY').val(Math.round(qrPos.top));
    }
}

function moveElement(elementType, direction) {
    const stepSize = 5; // pixels to move per click
    let element, xInput, yInput;

    // Determine which element and inputs to work with
    switch(elementType) {
        case 'name':
            element = $('#nameElement');
            xInput = $('#namePositionX');
            yInput = $('#namePositionY');
            break;
        case 'code':
            element = $('#codeElement');
            xInput = $('#codePositionX');
            yInput = $('#codePositionY');
            break;
        case 'qr':
            element = $('#qrElement');
            xInput = $('#qrPositionX');
            yInput = $('#qrPositionY');
            break;
        default:
            return;
    }

    // Get current position
    let currentX = parseInt(xInput.val()) || 0;
    let currentY = parseInt(yInput.val()) || 0;

    // Calculate new position based on direction
    switch(direction) {
        case 'up':
            currentY = Math.max(0, currentY - stepSize);
            break;
        case 'down':
            currentY += stepSize;
            break;
        case 'left':
            currentX = Math.max(0, currentX - stepSize);
            break;
        case 'right':
            currentX += stepSize;
            break;
    }

    // Update inputs
    xInput.val(currentX);
    yInput.val(currentY);

    // Update element position
    element.css({
        left: currentX + 'px',
        top: currentY + 'px'
    });

    updateCoordinateDisplays();
}

function handleFileUpload() {
    const file = $('#templateFile')[0].files[0];
    if (!file) return;

    console.log('Handling file upload:', file.name);

    // Validate file
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!validTypes.includes(file.type)) {
        alert('Please select a valid image (JPG, PNG) file.');
        return;
    }

    if (file.size > 10 * 1024 * 1024) {
        alert('File size must be less than 10MB.');
        return;
    }

    // Show loading
    showLoadingOverlay();

    // Create preview immediately using FileReader
    const reader = new FileReader();
    reader.onload = function(e) {
        displayTemplateImage(e.target.result);
    };
    reader.readAsDataURL(file);

    // Upload file to server
    const formData = new FormData();
    formData.append('background_image', file);
    formData.append('_token', $('meta[name="_token"]').attr('content') || '{{ csrf_token() }}');

    $.ajax({
        url: '{{ route("postEditEventTicketTemplate", ["event_id" => $event->id]) }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            hideLoadingOverlay();
            if (response.status === 'success') {
                alert('Template uploaded successfully!');
                console.log('Template uploaded successfully');
            } else {
                alert('Upload failed: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            hideLoadingOverlay();
            console.error('Upload error:', xhr);
            alert('Failed to upload template. Please try again.');
        }
    });
}

function displayTemplateImage(imageSrc) {
    console.log('Displaying template image...');

    // Remove placeholder
    $('#previewPlaceholder').remove();

    // Remove existing background image
    $('#templateBackground').remove();

    // Add new background image
    const img = $('<img>', {
        src: imageSrc,
        class: 'template-background',
        id: 'templateBackground'
    }).css({
        position: 'absolute',
        top: 0,
        left: 0,
        maxWidth: '100%',
        maxHeight: '100%',
        zIndex: 1,
        objectFit: 'contain'
    });

    $('#ticketPreview').prepend(img);

    // Reinitialize draggable elements
    setTimeout(function() {
        initializeDraggableElements();
    }, 100);

    console.log('Template image displayed');
}

function updateElementStyles() {
    console.log('Updating element styles...');

    // Update name element
    const nameSize = $('#nameFontSize').val();
    const nameColor = $('#nameFontColor').val();
    $('#nameElement').css({
        'font-size': nameSize + 'px',
        'color': nameColor
    });

    // Update code element
    const codeSize = $('#codeFontSize').val();
    const codeColor = $('#codeFontColor').val();
    $('#codeElement').css({
        'font-size': codeSize + 'px',
        'color': codeColor
    });

    // Update QR element
    const qrSize = $('#qrSize').val();
    $('#qrElement .qr-placeholder').css({
        'width': qrSize + 'px',
        'height': qrSize + 'px'
    });

    console.log('Element styles updated');
}

function updateCoordinateDisplays() {
    const namePos = $('#nameElement').position();
    const codePos = $('#codeElement').position();
    const qrPos = $('#qrElement').position();

    if (namePos) {
        $('#nameCoords').text('x: ' + Math.round(namePos.left) + ', y: ' + Math.round(namePos.top));
    }
    if (codePos) {
        $('#codeCoords').text('x: ' + Math.round(codePos.left) + ', y: ' + Math.round(codePos.top));
    }
    if (qrPos) {
        $('#qrCoords').text('x: ' + Math.round(qrPos.left) + ', y: ' + Math.round(qrPos.top));
    }
}

// Store the preview dimensions when the image loads
$(document).on('load', '#templateBackground', function() {
    console.log('Template background image loaded, storing dimensions');
    storePreviewDimensions();
});

// Store preview dimensions when saving template
function storePreviewDimensions() {
    const previewImg = $('#templateBackground');
    if (previewImg.length) {
        const previewWidth = previewImg.width();
        const previewHeight = previewImg.height();

        console.log(`Storing preview dimensions: ${previewWidth}x${previewHeight}`);

        // Add hidden fields to store these dimensions
        if ($('#previewWidth').length === 0) {
            $('<input>').attr({
                type: 'hidden',
                id: 'previewWidth',
                name: 'preview_width',
                value: previewWidth
            }).appendTo('#ticketForm');
        } else {
            $('#previewWidth').val(previewWidth);
        }

        if ($('#previewHeight').length === 0) {
            $('<input>').attr({
                type: 'hidden',
                id: 'previewHeight',
                name: 'preview_height',
                value: previewHeight
            }).appendTo('#ticketForm');
        } else {
            $('#previewHeight').val(previewHeight);
        }
    }
}

// Modify the saveTemplate function to include preview dimensions
function saveTemplate() {
    console.log('Saving template with preview dimensions...');

    // Store dimensions before saving
    storePreviewDimensions();

    // Get positions from inputs (more accurate than element positions)
    const nameX = parseInt($('#namePositionX').val()) || 0;
    const nameY = parseInt($('#namePositionY').val()) || 0;
    const codeX = parseInt($('#codePositionX').val()) || 0;
    const codeY = parseInt($('#codePositionY').val()) || 0;
    const qrX = parseInt($('#qrPositionX').val()) || 0;
    const qrY = parseInt($('#qrPositionY').val()) || 0;

    const previewWidth = $('#previewWidth').val() || $('#ticketPreview').width();
    const previewHeight = $('#previewHeight').val() || $('#ticketPreview').height();

    const data = {
        name_position_x: nameX,
        name_position_y: nameY,
        code_position_x: codeX,
        code_position_y: codeY,
        qr_position_x: qrX,
        qr_position_y: qrY,
        name_font_size: $('#nameFontSize').val(),
        name_font_color: $('#nameFontColor').val(),
        code_font_size: $('#codeFontSize').val(),
        code_font_color: $('#codeFontColor').val(),
        qr_size: $('#qrSize').val(),
        preview_width: previewWidth,
        preview_height: previewHeight,
        _token: $('meta[name="_token"]').attr('content') || '{{ csrf_token() }}'
    };

    console.log('Save data with dimensions:', data);

    $('#saveTemplate').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: '{{ route("saveTicketPositions", ["event_id" => $event->id]) }}',
        type: 'POST',
        data: data,
        success: function(response) {
            console.log('Save response:', response);
            if (response.status === 'success') {
                alert('Template settings saved successfully!');
            } else {
                alert('Save failed: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            console.error('Save error:', xhr);
            alert('Failed to save template settings. Please try again.');
        },
        complete: function() {
            $('#saveTemplate').prop('disabled', false).html('<i class="ico-save"></i> Save Template Settings');
        }
    });
}

// Add image onload handler to store dimensions when image loads
$(document).on('load', '#templateBackground', function() {
    storePreviewDimensions();
});

// Store dimensions when window is resized
$(window).on('resize', function() {
    setTimeout(storePreviewDimensions, 500);
});

function showLoadingOverlay() {
    const overlay = $('<div class="loading-overlay"><div class="spinner"></div></div>');
    $('#ticketPreview').append(overlay);
}

function hideLoadingOverlay() {
    $('.loading-overlay').remove();
}
</script>
@stop
