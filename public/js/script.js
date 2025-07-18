function download(e, t, i) {
    var o = new Blob([e], { type: i });
    if (window.navigator.msSaveOrOpenBlob)
      window.navigator.msSaveOrOpenBlob(o, t);
    else {
      var n = document.createElement("a"),
        a = URL.createObjectURL(o);
      (n.href = a),
        (n.download = t),
        document.body.appendChild(n),
        n.click(),
        setTimeout(function () {
          document.body.removeChild(n), window.URL.revokeObjectURL(a);
        }, 0);
    }
  }
  function format(e, t) {
    for (
      var i,
        o = new Array(1 + t++).join("  "),
        n = new Array(t - 1).join("  "),
        a = 0;
      a < e.children.length;
      a++
    )
      (i = document.createTextNode("\n" + o)),
        e.insertBefore(i, e.children[a]),
        format(e.children[a], t),
        e.lastElementChild == e.children[a] &&
          ((i = document.createTextNode("\n" + n)), e.appendChild(i));
    return e;
  }
  var dialogConfig = {
      title: "About",
      body: {
        type: "panel",
        items: [
          {
            type: "htmlpanel",
            html: '<h2>HTMEditor Version 0.5</h2>Online Free WYSIWYG HTML Editor.<br><a href="https://htmeditor.com" target="_blank"><b>HTM</b>Editor.com</a><br><br>Built on the <a href="https://www.tiny.cloud/docs/" target="_blank">Tinymce Version 5</a> engine.<br><a href="https://www.tiny.cloud/docs/" target="_blank">https://www.tiny.cloud</a>',
          },
        ],
      },
      buttons: [{ type: "cancel", name: "closeButton", text: "OK" }],
      initialData: {},
      onSubmit: function (e) {
        e.close();
      },
    },
    Init = function (e, t = !1, i = 480) {
      "yes" == t &&
        (window.onresize = function () {
          (tinymce.activeEditor.editorContainer.style.height =
            window.innerHeight + "px"),
            (tinymce.activeEditor.editorContainer.style.width =
              window.innerWidth + "px");
        }),
        tinymce.init({
          apply_source_formatting: !1,
          remove_linebreaks: !1,
          verify_html: !1,
          quickbars_insert_toolbar: "",
          branding: !1,
          selector: "textarea#" + e,
          plugins:
            "print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons",
          imagetools_cors_hosts: ["picsum.photos"],
          toolbar:
            "preview | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | table | undo redo | bold italic underline strikethrough | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | print | insertfile image media template link anchor codesample | ltr rtl | removeformat",
          toolbar_sticky: !0,
          autosave_ask_before_unload: !0,
          autosave_interval: "30s",
          autosave_prefix: "{path}{query}-{id}-",
          autosave_restore_when_empty: !1,
          autosave_retention: "2m",
          image_advtab: !0,
          content_css:
            "https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css",
          content_style: "body {padding: 10px}",
          menu: {
            custom_file: {
              title: "File",
              items:
                "newdocument basicitem | openfile saveas  | code preview | print",
            },
            custom_tools: {
              title: "Tools",
              items: "code wordcount | convert_to_app | recommended_hosting",
            },
            custom_help: { title: "Help", items: "help | costum_about" },
          },
          menubar:
            "custom_file edit view insert format custom_tools table, custom_help",
          importcss_append: !0,
          mobile: {
            theme: "silver",
            plugins:
              "print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons",
            toolbar:
              "preview | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | table | undo redo | bold italic underline strikethrough | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | print | insertfile image media template link anchor codesample | ltr rtl",
            menubar:
              "custom_file edit view insert format custom_tools table, custom_help",
          },
          file_picker_callback: function (e, t, i) {
            "file" === i.filetype &&
              e("https://www.google.com/logos/google.jpg", { text: "My text" }),
              "image" === i.filetype &&
                e("https://www.google.com/logos/google.jpg", {
                  alt: "My alt text",
                }),
              "media" === i.filetype &&
                e("movie.mp4", {
                  source2: "alt.ogg",
                  poster: "https://www.google.com/logos/google.jpg",
                });
          },
          templates: [
            {
              title: "New Table",
              description: "creates a new table",
              content:
                '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>',
            },
            {
              title: "Starting my story",
              description: "A cure for writers block",
              content: "Once upon a time...",
            },
            {
              title: "New list with dates",
              description: "New List with dates",
              content:
                '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>',
            },
          ],
          template_cdate_format: "[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]",
          template_mdate_format: "[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]",
          image_caption: !0,
          quickbars_selection_toolbar:
            "bold italic | quicklink h2 h3 blockquote quickimage quicktable",
          noneditable_noneditable_class: "mceNonEditable",
          toolbar_mode: "sliding",
          contextmenu: "link image imagetools table",
          height: parseInt(i),
          mode: "none",
          setup: function (e) {
            e.ui.registry.addMenuItem("basicitem", {
              text: "New window document",
              icon: "duplicate",
              onAction: function () {
                window.open("/author/", "_blank");
              },
            }),
              e.ui.registry.addMenuItem("openfile", {
                text: "Open File",
                icon: "browse",
                onAction: function () {
                  document
                    .getElementById("htmeditor-file-input-open-file")
                    .addEventListener("change", (e) => {
                      var t = e.target.files[0];
                      if ("text/html" == t.type) {
                        const e = new FileReader();
                        e.addEventListener("load", (e) => {
                          tinymce.activeEditor.setContent(e.target.result);
                        }),
                          e.readAsText(t, "UTF-8");
                      }
                    }),
                    document
                      .getElementById("htmeditor-file-input-open-file")
                      .click();
                },
              }),
              e.ui.registry.addMenuItem("saveas", {
                text: "Save As",
                icon: "save",
                onAction: function () {
                  console.log(tinyMCE.activeEditor.getContent());
                  var e = tinyMCE.activeEditor.getContent(),
                    t = document.createElement("div");
                  (t.innerHTML = e.trim()),
                    download(format(t, 0).innerHTML, "mypage.html", "text/html");
                },
              }),
              e.ui.registry.addMenuItem("costum_about", {
                text: "About",
                icon: "info",
                onAction: function () {
                  e.windowManager.open(dialogConfig);
                },
              }),
              e.ui.registry.addMenuItem("convert_to_app", {
                text: "Convert To Mobile App",
                icon: "reload",
                onAction: function () {
                  window.open("https://webintoapp.com?ref=htmeditor", "_blank");
                },
              }),
              e.ui.registry.addMenuItem("recommended_hosting", {
                text: "Recommended Web Hosting Services",
                icon: "checkmark",
                onAction: function () {
                  window.open(
                    "https://webhosting4me.com?ref=htmeditor",
                    "_blank",
                  );
                },
              }),
              e.on("init", function (i) {
                "yes" == t && e.execCommand("mceFullScreen");
              });
          },
          paste_as_text: true,
        });
    },
    loadScript = function (e, t) {
      var i = document.createElement("script");
      (i.type = "text/javascript"),
        (i.src = e),
        (i.onreadystatechange = t),
        (i.onload = t),
        document.getElementById(htmeditor_textarea).prepend(i);
    },
    load_js_callback = function () {
      Init(htmeditor_textarea, full_screen, editor_height);
    },
    htmeditor_textarea =
      document.currentScript.getAttribute("htmeditor_textarea"),
    full_screen =
      null == document.currentScript.getAttribute("full_screen")
        ? "no"
        : document.currentScript.getAttribute("full_screen"),
    editor_height =
      null == document.currentScript.getAttribute("editor_height")
        ? "480"
        : document.currentScript.getAttribute("editor_height"),
    enter_mode = "p",
    force_p_newlines = true,
            forced_root_block =  "p", 
            paste_as_text=true, 
            paste_auto_cleanup_on_paste = true,
    run_local =
      null == document.currentScript.getAttribute("run_local")
        ? "no"
        : document.currentScript.getAttribute("run_local"),
    file = document.createElement("input");
  file.setAttribute("id", "htmeditor-file-input-open-file"),
    file.setAttribute("type", "file"),
    file.setAttribute("accept", ".html, .html"),
    file.setAttribute("style", "display: none;"),
    document.getElementById(htmeditor_textarea).prepend(file),
    loadScript(
      "yes" == run_local
        ? "./js/tinymce.min.js"
        : "https://htmeditor.com/js/tinymce/tinymce.min.js",
      load_js_callback,
    );
  



function hideCookie() {
    const CookieDate = new Date();
    CookieDate.setFullYear(CookieDate.getFullYear() + 1);
    // CookieDate.setSeconds(CookieDate.getSeconds() + 10);

    // Set cookie
    document.cookie = 'infoCookies=true; expires=' + CookieDate.toUTCString() + '; path=/';

    // Hide the popup
    const popup = document.getElementById("cookies");
    if (popup) popup.style.display = "none";
}
