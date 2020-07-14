
var PageBuilder = function(){
    'use strict';

    var $option = {};

	var $defaultOption = {
        editor: '#editor',
        container : '#gjs',
        fromElement: true,
        width: 'auto',
        height: 'calc(100vh - 40px)',
        jsInHtml: false,
        noticeOnUnload: false,
        storageManager: { type: 0 },

        /*
        storageManager: {
            type: 'local',          // Type of the storage
            autosave: true,         // Store data automatically
            autoload: true,         // Autoload stored data on init
        },
        */
        domComponents: {
            wrapper: {
                copyable: false,
                draggable: false,
                removable: false,
                movable: false,
                droppable: 'section, aside, header, footer',
            },
        },
        plugins: [
            'pagebuilder',
        ],
        panels: {
            defaults: []
        },
        deviceManager: {
            appendTo: '.panel-device',
        },
        layerManager: {
            appendTo: '.panel-layer'
        },
        selectorManager: {
            appendTo: '.panel-style',
            componentFirst: 1,
        },
        traitManager: {
            appendTo: '.panel-style',
        },
        styleManager: {
            appendTo: '.panel-style',
        },
        blockManager: {
            appendTo: '.panel-block',
        },
    };

    var $defaultSaveElement = {
        'html': '#htmlContent',
        'json': '#jsonContent',
        'css': '#cssContent',
    }

    var $editorHtml = `
    <div class="panel-top d-flex flex-row">
        <div class="panel-top-left"></div>
        <div class="panel-top-center"></div>
        <div class="panel-top-right"></div>
        <div class="panel-top-preview"></div>
        <div class="panel-top-style"></div>
    </div>
    <div class="panel-middle d-flex flex-row">
        <div class="panel-middle-left">

            <div class="block-manager d-none">
                <div class="bg-primary text-white p-10 px-3">
                    <i class="fas fa-th-large"></i> 
                    <span class="letter-spacing-1">Block</span>
                </div>
                
                <div class="p-10">
                    <select class="selectpicker form-control block-selector" data-width="100%">
                        <option value="all">All</option>
                    </select>
                </div>

                <div class="panel-block"></div>
            </div>

            <div class="layer-manager d-none">
                <div class="bg-info text-white p-10 px-3">
                    <i class="fas fa-layer-group"></i> 
                    <span class="letter-spacing-1">Layer</span>
                </div>

                <div class="panel-layer"></div>
            </div>

        </div>
        <div class="panel-middle-center">
            <div id="gjs"></div>
        </div>
        <div class="panel-middle-right">

            <div class="style-manager d-none">
                <div class="bg-danger text-white p-10 px-3">
                    <i class="fas fa-palette"></i> 
                    <span class="letter-spacing-1">Style</span>
                </div>

                <div class="panel-style"></div>
            </div>
        </div>
    </div>`;

    var $editor,
        $deviceManager,
        $panelManager,
        $selectorManager,
        $traitManager,
        $styleManager,
        $blockManager,
        $blocksAll,
        $command,
        $domComponents,
        $defaultType,
        $defaultModel,
        $defaultView,
        $textType,
        $textModel,
        $textView,
        $linkType,
        $linkModel,
        $linkView,
        $imageType,
        $imageModel,
        $imageView
        ;

    var init = function(editor)
    {
        $editor = editor;
        $deviceManager = editor.DeviceManager;
        $panelManager = editor.Panels;
        $selectorManager = editor.SelectorManager;
        $traitManager = editor.TraitManager;
        $styleManager = editor.StyleManager;
        $command = editor.Commands;
        $domComponents = editor.DomComponents;
        $blockManager = editor.BlockManager;

        $defaultType = $domComponents.getType('default');
        $defaultModel = $defaultType.model;
        $defaultView = $defaultType.view;
        $textType = $domComponents.getType('text');
        $textModel = $textType.model;
        $textView = $textType.view;
        $linkType = $domComponents.getType('link');
        $linkModel = $linkType.model;
        $linkView = $linkType.view;
        $imageType = $domComponents.getType('image');
        $imageModel = $imageType.model;
        $imageView = $imageType.view;

        panels.init();

        editor.on('load', function() {
            // Add Settings Sector
            var traitsSector = $('<div class="gjs-sm-sectors gjs-one-bg gjs-two-color">'+
                '<div class="gjs-sm-sector gjs-sm-sector__typography no-select gjs-sm-open">'+
                '<div class="gjs-sm-title"><i id="gjs-sm-caret" class="fa fa-caret-right"></i> Settings</div>' +
                '<div class="gjs-sm-properties" style="display: block;"></div>'+
            '</div></div>');
            var traitsProps = traitsSector.find('.gjs-sm-properties');
            traitsProps.append($('.gjs-trt-traits'));
            $('.gjs-sm-sectors').before(traitsSector);
            traitsSector.find('.gjs-sm-title').on('click', function(){
                var traitStyle = traitsProps.get(0).style;
                var hidden = traitStyle.display == 'none';
                if (hidden) {
                    traitStyle.display = 'block';
                } else {
                    traitStyle.display = 'none';
                }
            });
        });

        editor.on('component:selected', function() {

            // get the selected componnet and its default toolbar
            const selectedComponent = editor.getSelected();
            const defaultToolbar = selectedComponent.get('toolbar');

            defaultToolbar.forEach((value, index)=>{
                switch(value.command){
                    case 'tlb-delete':
                        value.attributes.class = 'fas fa-trash';
                    break;
                    case 'tlb-clone':
                        value.attributes.class = 'fas fa-copy';
                    break;
                }
            });

            selectedComponent.set({
                toolbar: [ ...defaultToolbar ],
            });
        });
    }

    var panels = {
        init: function(){
            this.block();
            this.device();
            this.action();
            this.style();
            this.tooltip();
        },
        block: function(){
            $panelManager.addPanel({
                id: 'panel-top-left',
                el: '.panel-top-left',
            });

            $panelManager.addButton('panel-top-left', [
                {
                    id: 'btn-block',
                    label: '<i class="fa fa-plus"></i>',
                    command: 'btn-block',
                    attributes: {'title': 'Block'},
                    active: true,
                    togglable: true,
                },
                {
                    id: 'btn-layer',
                    label: '<i class="fas fa-layer-group"></i>',
                    command: 'btn-layer',
                    attributes: {'title': 'Layer'},
                    togglable: true,
                }
            ]);

            $command.add('btn-block', {
                run(editor, sender) {
                    document.querySelector(".panel-middle-left").classList.add('panel-middle-left-open');
                    document.querySelector(".block-manager").classList.remove('d-none');
                    $editor.refresh();
                },
                stop(editor, sender) {
                    document.querySelector(".panel-middle-left").classList.remove('panel-middle-left-open');
                    document.querySelector(".block-manager").classList.add('d-none');
                    $editor.refresh();
                },
            });

            $command.add('btn-layer', {
                run(editor, sender) {
                    document.querySelector(".panel-middle-left").classList.add('panel-middle-left-open');
                    document.querySelector(".layer-manager").classList.remove('d-none');
                    $editor.refresh();
                },
                stop(editor, sender) {
                    document.querySelector(".panel-middle-left").classList.remove('panel-middle-left-open');
                    document.querySelector(".layer-manager").classList.add('d-none');
                    $editor.refresh();
                },
            });
        },
        device: function(){
            $panelManager.addPanel({
                id: 'panel-top-center',
                el: '.panel-top-center',
            });

            $panelManager.addButton('panel-top-center', [
                {
                    id: 'deviceXl',
                    className: "fas fa-tv", 
                    command: {
                        run: function(ed) { ed.setDevice('Extra Large') },
                        stop: function() {},
                    },
                    text: 'XL',
                    attributes: {'title': 'Extra Large'},
                    togglable: false,
                    active: true
                },
                {
                    id: 'deviceLg',
                    className: "fas fa-desktop", 
                    command: {
                        run: function(ed) { ed.setDevice('Large') },
                        stop: function() {},
                    },
                    text: 'Large',
                    attributes: {'title': 'Large'},
                    togglable: false,
                },
                {
                    id: 'deviceMd',
                    className: "fas fa-laptop", 
                    command: {
                        run: function(ed) { ed.setDevice('Medium') },
                        stop: function() {},
                    },
                    text: 'Medium',
                    attributes: {'title': 'Medium'},
                    togglable: false,
                },
                {
                    id: 'deviceSm',
                    className: "fas fa-tablet-alt", 
                    command: {
                        run: function(ed) { ed.setDevice('Small') },
                        stop: function() {},
                    },
                    text: 'Small',
                    attributes: {'title': 'Small'},
                    togglable: false,
                },
                {
                    id: 'deviceXs',
                    className: "fas fa-mobile", 
                    command: {
                        run: function(ed) { ed.setDevice('Extra Small') },
                        stop: function() {},
                    },
                    text: 'XS',
                    attributes: {'title': 'Extra Small'},
                    togglable: false,
                }
            ]);

            var $windowWidth = window.innerWidth > 1300 ? window.innerWidth : 1300;

            $deviceManager.add('Extra Large', '', {
                name: 'Extra Large',
            });

            $deviceManager.add('Large', '1199px', {
                name: 'Large',
                //widthMedia: '1200px'
            });

            $deviceManager.add('Medium', '991px', {
                name: 'Medium',
                widthMedia: '992px'
            });

            $deviceManager.add('Small', '767px', {
                name: 'Small',
                widthMedia: '768px'
            });

            $deviceManager.add('Extra Small', '574px', {
                name: 'Extra Small',
                widthMedia: '580px'
            });
        },
        action: function(){
            $panelManager.addPanel({
                id: 'panel-top-right',
                el: '.panel-top-right',
            });

            $panelManager.addButton('panel-top-right', [
                {
                    id: 'sw-visibility',
                    className: 'btn-toggle-borders',
                    label: '<i class="fas fa-border-none"></i>',
                    command: 'sw-visibility',
                    active: true,
                }
            ]);

            $panelManager.addPanel({
                id: 'panel-top-preview',
                el: '.panel-top-preview',
            });

            $panelManager.addButton('panel-top-preview', [
                {
                    id: 'preview',
                    className: 'btn-preview',
                    label: '<i class="fas fa-eye"></i>',
                    command: 'preview',
                    active: false,
                },
                {
                    id: 'canvas-clear',
                    className: 'btn-canvas-clear',
                    label: '<i class="fas fa-trash"></i>',
                    command: 'canvas-clear',
                    active: false,
                },
                {
                    id: 'fullscreen',
                    className: 'btn-toggle-borders',
                    label: '<i class="fas fa-expand-arrows-alt"></i>',
                    command: 'fullscreen',
                    active: false,
                },
            ]);

            $editor.on('run:preview', () => {
                $('.panel-top, .panel-middle-left, .panel-middle-right').addClass('gjs-hidden');
            });
            $editor.on('stop:preview', () => {
                $('.panel-top, .panel-middle-left, .panel-middle-right').removeClass('gjs-hidden');
            });

            $command.add('canvas-clear', {
                run(editor, sender, opts = {}) {
                    var $components = $editor.DomComponents.getComponents();
                    var $comp = [];
                    $components.forEach(component => {
                        if (!component || !component.get('removable')) {
                            console.warn('The element is not removable', component);
                            return;
                        }

                        if(component.get('tagName')=='header' || component.get('tagName')=='footer'){
                            console.warn('The element is not removable', component);
                            return;
                        }

                        if(component){
                            $comp.push(component);
                        }
                    });

                    for(var $i=0; $i<$comp.length; $i++){
                        const coll = $comp[$i].collection;
                        $comp[$i].trigger('component:destroy');
                        coll && coll.remove($comp[$i]);
                    }

                    //$editor.DomComponents.clear();
                    //$editor.CssComposer.clear();
                },
            });
        },
        style: function(){

            $panelManager.addPanel({
                id: 'panel-top-style',
                el: '.panel-top-style',
            });

            $panelManager.addButton('panel-top-style', [
                {
                    id: 'btn-style',
                    active: true,
                    className: 'btn-style',
                    label: '<i class="fas fa-palette"></i>',
                    command: 'btn-style',
                }
            ]);

            $command.add('btn-style', {
                run(editor, sender) {
                    document.querySelector(".panel-middle-right").classList.add('panel-middle-right-open');
                    document.querySelector(".style-manager").classList.remove('d-none');
                    $editor.refresh();
                },
                stop(editor, sender) {
                    document.querySelector(".panel-middle-right").classList.remove('panel-middle-right-open');
                    document.querySelector(".style-manager").classList.add('d-none');
                    $editor.refresh();
                },
            });

            $styleManager.addSector('typography', {
                name: 'Typography',
                open: false,
                buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'font-style', 'text-shadow'],
                properties:[
                    { name: 'Font', property: 'font-family'},
                    { name: 'Weight', property: 'font-weight'},
                    { name: 'Font color', property: 'color'},
                    {
                        property: 'text-align',
                        type: 'radio',
                        defaults: 'left',
                        list: [
                            { value : 'left',  name : 'Left',    className: 'fa fa-align-left'},
                            { value : 'center',  name : 'Center',  className: 'fa fa-align-center' },
                            { value : 'right',   name : 'Right',   className: 'fa fa-align-right'},
                            { value : 'justify', name : 'Justify',   className: 'fa fa-align-justify'}
                        ],
                    },
                    {
                        property: 'text-decoration',
                        type: 'radio',
                        defaults: 'none',
                        list: [
                            { value: 'none', name: 'None', className: 'fa fa-times'},
                            { value: 'underline', name: 'underline', className: 'fa fa-underline' },
                            { value: 'line-through', name: 'Line-through', className: 'fa fa-strikethrough'}
                        ],
                    },
                    {
                        property:'font-style',
                        type: 'radio',
                        defaults: 'normal',
                        list:[
                            {value:'normal',name:'Normal',className:'fa fa-font'},
                            {value:'italic',name:'Italic',className:'fa fa-italic'}
                        ],
                    },
                    {
                        property: 'text-shadow',
                        properties: [
                            { name: 'X position', property: 'text-shadow-h'},
                            { name: 'Y position', property: 'text-shadow-v'},
                            { name: 'Blur', property: 'text-shadow-blur'},
                            { name: 'Color', property: 'text-shadow-color'}
                        ],
                    },
                ],
            });

            $styleManager.addSector('border', {
                name: 'Border',
                open: false,
                buildProps: ['border', 'border-radius'],
                properties: [
                    {
                        property: 'border-radius',
                        properties  : [
                            { name: 'Top', property: 'border-top-left-radius'},
                            { name: 'Right', property: 'border-top-right-radius'},
                            { name: 'Bottom', property: 'border-bottom-left-radius'},
                            { name: 'Left', property: 'border-bottom-right-radius'}
                        ],
                    },
                ]
            });

            $styleManager.addSector('decorations', {
                name: 'Decorations',
                open: false,
                buildProps: ['opacity', 'background-color', 'background', 'box-shadow'],
                properties: [
                    {
                        type: 'slider',
                        property: 'opacity',
                        defaults: 1,
                        step: 0.01,
                        max: 1,
                        min:0,
                    },
                    {
                        property: 'background',
                        properties: [
                            { name: 'Image', property: 'background-image'},
                            { name: 'Repeat', property:   'background-repeat'},
                            { name: 'Position', property: 'background-position'},
                            { name: 'Attachment', property: 'background-attachment'},
                            { name: 'Size', property: 'background-size'}
                        ],
                    },
                    {
                        property: 'box-shadow',
                        properties: [
                            { name: 'X position', property: 'box-shadow-h'},
                            { name: 'Y position', property: 'box-shadow-v'},
                            { name: 'Blur', property: 'box-shadow-blur'},
                            { name: 'Spread', property: 'box-shadow-spread'},
                            { name: 'Color', property: 'box-shadow-color'},
                            { name: 'Shadow type', property: 'box-shadow-type'}
                        ],
                    },
                ]
            });

            $styleManager.addSector('border', {
                name: 'Layout',
                open: false,
                buildProps: ['width', 'height', 'max-width', 'min-height', 'margin', 'padding'],
                properties: [
                    {
                        property: 'margin',
                        properties:[
                            { name: 'Top', property: 'margin-top'},
                            { name: 'Right', property: 'margin-right'},
                            { name: 'Bottom', property: 'margin-bottom'},
                            { name: 'Left', property: 'margin-left'}
                        ],
                    },
                    {
                        property  : 'padding',
                        properties:[
                            { name: 'Top', property: 'padding-top'},
                            { name: 'Right', property: 'padding-right'},
                            { name: 'Bottom', property: 'padding-bottom'},
                            { name: 'Left', property: 'padding-left'}
                        ],
                    }
                ]
            });
        },
        tooltip: function(){
            [
                ['btn-block', 'Show Blocks', 'panel-top-left'], 
                ['btn-layer', 'Show Layers', 'panel-top-left'], 

                ['deviceXl', 'Large Desktop', 'panel-top-center'], 
                ['deviceLg', 'Desktop', 'panel-top-center'], 
                ['deviceMd', 'Labtop', 'panel-top-center'], 
                ['deviceSm', 'Pad', 'panel-top-center'], 
                ['deviceXs', 'Mobile', 'panel-top-center'], 

                ['sw-visibility', 'Show Borders', 'panel-top-right'], 
                ['preview', 'Preview', 'panel-top-preview'],

                ['btn-style', 'Style Manager', 'panel-top-style'],
            ]
            .forEach(function(item) {
                $panelManager.getButton(item[2], item[0]).set('attributes', {'data-tooltip-pos': 'bottom', 'data-tooltip': item[1]});
            });
        }
    }

    var gjs = function($config){
        $option = $.extend({}, $defaultOption, $config);
        if(!$option.plugins.includes('pagebuilder')){
            $option.plugins.push('pagebuilder');
        }
        $($option.editor).append($editorHtml);
        var $editor = grapesjs.init($option);

        var $htmlText = '<textarea id="htmlContent" name="htmlContent" style="visibility: hidden;"></textarea>';
        var $jsonText = '<textarea id="jsonContent" name="jsonContent" style="visibility: hidden;"></textarea>';
        var $cssText = '<textarea id="cssContent" name="cssContent" style="visibility: hidden;"></textarea>';
        $($option.editor).after($htmlText+$jsonText+$cssText);

        $command.add('open-assets', {
            run(editor) {
                $(this).media({'editor': editor});
            }
        })
    }

    var save = function($element){
        $element = $.extend({}, $defaultSaveElement, $element);
        $($element.html).val($editor.getHtml());
        $($element.json).val(JSON.stringify($editor.getComponents()));
        $($element.css).val($editor.getCss());
        //$($text_id).val($editor.getHtml());
    }

    var setUrl = function($url){
        var $selected = $editor.getSelected();
        switch($selected.attributes.tagName){
            case 'a':
                $selected.attributes.attributes = {href: $url};
                $selected.trigger('change:attributes:href');
            break;
            case 'img':
                $selected.set('src', $url);
            break;
            default:
                $selected.setStyle({ 'background': 'url("'+$url+'")' });
            break;
        }
    }

    var load = function($json, $css=''){
        if($json){
            $editor.setComponents($json);
        }
        if($css){
            $editor.setStyle($css);
        }
    }

    return {
        init: function(editor){
            init(editor);
        },
        gjs: function($config = {}){
            gjs($config);
        },
        save: function($element){
            save($element);
        },
        setUrl: function($url){
            setUrl($url);
        },
        load: function($json, $css){
            load($json, $css);
        }
    };
}();

function pagebuilder(editor)
{
    PageBuilder.init(editor);
}