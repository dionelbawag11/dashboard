bender.loadExternalPlugin("exportpdf","/apps/plugin/"),CKEDITOR.plugins.load("exportpdf",(function(){bender.test({setUp:function(){bender.tools.ignoreUnsupportedEnvironment("exportpdf")},"test data is correct at read and send stages":function(){bender.editorBot.create({name:"editor1",config:exportPdfUtils.getDefaultConfig("unit")},(function(e){var t=e.editor;e.setHtmlWithSelection('<p id="test">Hello, World!</p>^'),t.once("exportPdf",(function(e){assert.areEqual(e.data.html,t.getData(),"Data from editor is incorrect."),assert.isTrue(CKEDITOR.tools.isEmpty(e.data.options),"`options` object should be initially empty.")})),t.once("exportPdf",(function(e){e.cancel(),assert.areEqual('<div class="cke_editable cke_contents_ltr">'+t.getData()+"</div>",e.data.html,"Preprocessed data sent to endpoint is incorrect."),assert.isNotNull(e.data.css,"CSS should be attached.")}),null,null,16),t.execCommand("exportPdf")}))},"test options provided via config":function(){bender.editorBot.create({name:"editor2",config:exportPdfUtils.getDefaultConfig("unit",{exportPdf_options:{format:"A6"}})},(function(e){var t=e.editor;e.setHtmlWithSelection('<p id="test">Hello, World!</p>^'),t.once("exportPdf",(function(e){e.cancel(),assert.areEqual(e.data.options.format,"A6")})),t.execCommand("exportPdf")}))},"test html changed via event":function(){bender.editorBot.create({name:"editor3",config:exportPdfUtils.getDefaultConfig("unit")},(function(e){var t=e.editor;e.setHtmlWithSelection('<p id="test">Hello, World!</p>^'),t.once("exportPdf",(function(e){e.cancel(),assert.areEqual(e.data.html,"")})),t.once("exportPdf",(function(e){assert.areNotEqual(e.data.html,""),e.data.html=""}),null,null,1),t.execCommand("exportPdf")}))},"test options changed via event":function(){bender.editorBot.create({name:"editor4",config:exportPdfUtils.getDefaultConfig("unit")},(function(e){var t=e.editor;e.setHtmlWithSelection('<p id="test">Hello, World!</p>^'),t.once("exportPdf",(function(e){e.cancel(),assert.areEqual(e.data.options.format,"A5")})),t.once("exportPdf",(function(e){e.data.options.format="A5"}),null,null,1),t.execCommand("exportPdf")}))},"test html changed via event asynchronously":function(){bender.editorBot.create({name:"editor5",config:exportPdfUtils.getDefaultConfig("unit")},(function(e){var t=e.editor;e.setHtmlWithSelection('<p id="test">Hello, World!</p>^'),t.on("exportPdf",(function(e){e.cancel(),e.data.asyncDone&&(resume(),assert.areEqual(e.data.html,"<p>Content filtered!</p>"),delete e.data.asyncDone,assert.isUndefined(e.data.asyncDone))})),t.on("exportPdf",(function(e){e.data.asyncDone||setTimeout((function(){e.data.html="<p>Content filtered!</p>",e.data.asyncDone=!0,t.fire("exportPdf",e.data)}),1e3)}),null,null,1),t.execCommand("exportPdf"),wait()}))},"test options changed via event asynchronously":function(){bender.editorBot.create({name:"editor6",config:exportPdfUtils.getDefaultConfig("unit",{exportPdf_options:{format:"A5"}})},(function(e){var t=e.editor;e.setHtmlWithSelection('<p id="test">Hello, World!</p>^'),t.on("exportPdf",(function(e){e.cancel(),e.data.asyncDone&&(resume(),assert.areEqual(e.data.options.format,"A4"),delete e.data.asyncDone,assert.isUndefined(e.data.asyncDone))})),t.on("exportPdf",(function(e){e.data.asyncDone||setTimeout((function(){e.data.options.format="A4",e.data.asyncDone=!0,t.fire("exportPdf",e.data)}),1e3)}),null,null,1),t.execCommand("exportPdf"),wait()}))},"test default CKEditor config":function(){bender.editorBot.create({name:"editor7",config:exportPdfUtils.getDefaultConfig("unit")},(function(e){CKEDITOR.config.exportPdf_isDev?assert.areEqual(e.editor.config.exportPdf_service,"https://pdf-converter.cke-cs-staging.com/v1/convert","Default dev endpoint is incorrect."):assert.areEqual(e.editor.config.exportPdf_service,"https://pdf-converter.cke-cs.com/v1/convert","Default prod endpoint is incorrect."),assert.areEqual(e.editor.config.exportPdf_fileName,"ckeditor4-export-pdf.pdf","Default file name is incorrect.")}))},"test inaccessible stylesheets are handled correctly":function(){bender.editorBot.create({name:"editor8",config:exportPdfUtils.getDefaultConfig("unit",{contentsCss:"https://cdn.ckeditor.com/4.16.0/full-all/samples/css/samples.css"})},(function(e){var t=e.editor,o=!1,n=CKEDITOR.on("log",(function(e){"exportpdf-stylesheets-inaccessible"===e.data.errorCode&&(e.cancel(),CKEDITOR.removeListener("log",n),o=!0)}));e.setHtmlWithSelection('<p id="test">Hello, World!</p>^'),t.once("exportPdf",(function(e){e.cancel(),resume((function(){o?assert.pass():assert.fail("No errors thrown while accessing stylesheets rules.")}))}),null,null,19),CKEDITOR.tools.setTimeout((function(){t.execCommand("exportPdf")}),1e3),wait()}))}})}));