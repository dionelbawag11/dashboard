!(function () {
  var e,
    t = {
      50573: function (e, t, a) {
        "use strict";
        var n = a(38965);
        a.p = (0, n.amVar)("public-path");
        a(28594), a(35666);
        var i = a(65311),
          o = a.n(i);
        (window.jQuery = window.$ = o()),
          a(23084),
          a(92993),
          a(68181),
          a(62526),
          a(44855),
          a(91707),
          a(66709),
          a(87285),
          a(66177),
          a(33071),
          a(84414),
          a(61923),
          a(86227),
          a(92466),
          a(68573),
          a(25557),
          a(99047),
          a(40686),
          a(86569),
          a(67515),
          a(34594),
          a(36747),
          a(8146),
          a(68287),
          a(33673),
          a(10073),
          a(51573),
          a(77086),
          a(62320),
          a(96487),
          a(24332),
          a(43597),
          a(1e4),
          a(71059),
          a(98009),
          a(60580),
          a(61514),
          a(65100),
          a(9730),
          a(19997),
          a(38954),
          a(78257),
          a(48796),
          a(30381),
          a(63505);
      },
      35571: function () {
        var e,
          t,
          a = { select_condition_to_add: "select_condition_to_add" };
        function n() {
          return (
            jQuery(t)
              .toggle()
              .css("height", jQuery(window).height() / 3 + "px")
              .css("width", jQuery(window).width() / 3 + "px")
              .position({
                my: "center top",
                at: "center top",
                overflow: "fit",
                of: ".am-filter-wrap",
              }),
            !1
          );
        }
        function i() {
          return jQuery(t).hide(), !1;
        }
        function o(t) {
          var a = jQuery(t.target);
          a.toggleClass("active"),
            a.hasClass("active")
              ? jQuery(e)
                  .fadeIn(300)
                  .position({
                    my: "right top+30",
                    at: "right bottom",
                    of: ".am-filter-wrap",
                    overflow: "fit",
                  })
                  .position({
                    my: "right top",
                    at: "right bottom",
                    of: ".am-filter-wrap",
                    overflow: "fit",
                  })
              : jQuery(e)
                  .position({
                    my: "right top+30",
                    at: "right bottom",
                    of: ".am-filter-wrap",
                    overflow: "fit",
                  })
                  .fadeOut(300);
        }
        function r() {
          (e = document.getElementById("advanced-search")) &&
            ((t = document.getElementById("save-advanced-search")),
            (a = jQuery(e).data("translations")),
            jQuery(".delete", e).click(function () {
              var e = jQuery(this).parents(".searchField");
              e.hide(), jQuery(":input", e).prop("disabled", !0);
            }),
            jQuery("select#search-add-field", e).change(function () {
              if ("" != jQuery(this).val()) {
                var e = jQuery(this).val(),
                  t = jQuery("#" + e);
                t.show(),
                  jQuery(":input", t).prop("disabled", !1),
                  (this.selectedIndex = null);
              }
            }),
            jQuery("tr.empty", e).hide(),
            jQuery("tr.empty :input", e).prop("disabled", !0),
            jQuery("#search-add-field", e).select2({
              placeholder: "** " + a.select_condition_to_add + " **",
              width: "560px",
            }),
            jQuery(".am-advanced-search-toggle").off().on("click", o),
            jQuery(".am-save-advanced-search-hide").off().on("click", i),
            jQuery(".am-save-advanced-search").off().on("click", n));
        }
        jQuery(document).ready(function (e) {
          r();
        }),
          $(document).ajaxComplete(function () {
            setTimeout(function () {
              return r();
            }, 0);
          });
      },
      48796: function (e, t, a) {
        "use strict";
        a.r(t);
        a(69826), a(41539), a(54747), a(82526), a(41817);
        var n = a(3100),
          i =
            (a(32165),
            a(66992),
            a(78783),
            a(33948),
            a(91038),
            a(47042),
            a(68309),
            a(74916),
            a(86771),
            a(58399)),
          o = a.n(i);
        function r(e) {
          return (
            (function (e) {
              if (Array.isArray(e)) return l(e);
            })(e) ||
            (function (e) {
              if (
                ("undefined" != typeof Symbol && null != e[Symbol.iterator]) ||
                null != e["@@iterator"]
              )
                return Array.from(e);
            })(e) ||
            (function (e, t) {
              if (!e) return;
              if ("string" == typeof e) return l(e, t);
              var a = Object.prototype.toString.call(e).slice(8, -1);
              "Object" === a && e.constructor && (a = e.constructor.name);
              if ("Map" === a || "Set" === a) return Array.from(e);
              if (
                "Arguments" === a ||
                /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a)
              )
                return l(e, t);
            })(e) ||
            (function () {
              throw new TypeError(
                "Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."
              );
            })()
          );
        }
        function l(e, t) {
          (null == t || t > e.length) && (t = e.length);
          for (var a = 0, n = new Array(t); a < t; a++) n[a] = e[a];
          return n;
        }
        var s = !1;
        function c() {
          if (!s) {
            s = !0;
            var e = document.createElement("span");
            (e.innerHTML = o()),
              document.body.appendChild(e),
              (e.querySelector(".am-popup-close").onclick = function () {
                return d();
              });
          }
        }
        function d() {
          var e = document.querySelector(".am-body-blackout"),
            t = document.querySelector(".am-popup-modal");
          e.classList.remove("is-blacked-out"),
            t.classList.remove("am-is-visible");
        }
        var u = {
            init: c,
            show: function () {
              c(),
                document
                  .querySelector(".am-body-blackout")
                  .classList.add("is-blacked-out"),
                document
                  .querySelector(".am-popup-modal")
                  .classList.add("am-is-visible");
            },
            hide: d,
            width: function (e) {
              e < 1 && (e = window.innerWidth * e),
                r(document.getElementsByTagName("body")).forEach(function (t) {
                  t.style.setProperty("--blackout-width", e + "px"),
                    t.style.setProperty("--blackout-close-left", e + 3 + "px");
                });
            },
            height: function (e) {
              e < 1 && (e = (window.innerHeight - 160) * e),
                r(document.getElementsByTagName("body")).forEach(function (t) {
                  t.style.setProperty("--blackout-height", e + "px"),
                    t.style.setProperty(
                      "--blackout-popup-height",
                      e + 160 + "px"
                    );
                });
            },
            get_body: function () {
              return document.querySelector(".am-popup-modal .am-popup-body");
            },
          },
          p = a(29911),
          f = a.n(p);
        function h(e) {
          var t = e.querySelector('input[type="hidden"]'),
            a = e.querySelector(".am-advanced-search-text"),
            n = e.getAttribute("data-url");
          u.width(0.8), u.height(0.95), u.init();
          var i = u.get_body(),
            o = document.createElement("iframe");
          for (
            o.setAttribute("scrolling", "yes"),
              o.addEventListener("load", function () {
                u.show();
                var n = o.contentWindow.document.getElementById(
                  "_am_save_advanced_search"
                );
                if (n) {
                  for (u.hide(); i.firstChild; ) i.removeChild(i.firstChild);
                  var r = JSON.parse(n.getAttribute("data-vars"));
                  return (
                    t.setAttribute("value", r.conditions),
                    (e.getElementsByTagName("a")[0].textContent =
                      r.description),
                    void (a.innerHTML = r.text)
                  );
                }
                o.contentWindow.addEventListener("click", function (e) {
                  e.target.classList.contains(
                    "am-advanced-search-browse-open"
                  ) &&
                    (function (e) {
                      var t =
                          document._amSearchIframe ||
                          window.parent.document._amSearchIframe,
                        a =
                          document._amPopup || window.parent.document._amPopup,
                        n =
                          t.contentWindow.document.getElementsByTagName(
                            "form"
                          )[0],
                        i = jQuery(n).find(":input[name!='body']").serialize();
                      e = e + "&" + i;
                      var o = a.get_body(),
                        r = document.createElement("div");
                      (r.className = "am-advanced-search-browse-div"),
                        (r.innerHTML = f()),
                        r
                          .getElementsByClassName(
                            "am-advanced-search-browse-back"
                          )
                          .forEach(function (e) {
                            e.addEventListener("click", function () {
                              o.removeChild(r), (t.style.display = "block");
                            });
                          }),
                        r
                          .getElementsByClassName(
                            "am-advanced-search-browse-iframe"
                          )
                          .forEach(function (t) {
                            (t.src = e),
                              t.setAttribute("scrolling", "yes"),
                              t.addEventListener("load", function () {});
                          }),
                        (t.style.display = "none"),
                        o.appendChild(r);
                    })(e.target.getAttribute("data-url"));
                  if (e.target.classList.contains("am-advanced-search-save"))
                    o.contentWindow.document.getElementsByTagName("form")[0];
                });
              }),
              o.src = n,
              o.className = "am-advanced-search-iframe",
              document._amSearchIframe = o,
              document._amPopup = u;
            i.firstChild;

          )
            i.removeChild(i.firstChild);
          i.appendChild(o);
        }
        a(35571),
          a(69625),
          (0, n.Z)(function () {
            document.addEventListener("click", function (e) {
              e.target.classList.contains(
                "am-open-advanced-search-popup-open"
              ) && h(e.target.closest(".am-open-advanced-search-popup"));
            });
          });
      },
      1e4: function (e, t, a) {
        "use strict";
        a.r(t);
        a(74916), a(15306);
        var n = a(38965),
          i = a(60190);
        jQuery(function () {
          if ((0, n.scriptReplaced)("aff-info-tab")) {
            var e = (0, n.amVars)().affTabInfoUrl;
            jQuery(document).on("click", "a.aff-detail", function () {
              var t = this.getAttribute("data-from"),
                a = this.getAttribute("data-to"),
                n = this.getAttribute("data-label"),
                o = e
                  .replace(/FROMDATE/, encodeURIComponent(t))
                  .replace(/TODATE/, encodeURIComponent(a));
              jQuery("#aff-detail").load(o, function () {
                jQuery("#aff-detail").dialog({
                  title: (0, i.S)("Affiliate Commission Details for") + " " + n,
                  width: 700,
                  heigth: 300,
                });
              });
            });
          }
        });
      },
      43597: function (e, t, a) {
        "use strict";
        a.r(t);
        a(69826), a(41539), a(47042);
        var n = a(38965),
          i = a(60190);
        function o(e) {
          jQuery.getScript(e).fail(function () {
            o(e);
          });
        }
        function r() {
          var e = (0, n.amVars)(),
            t = (e.ticketMask, e.isLive),
            a = e.helpdeskUpdateUrl,
            r = e.ticketTitle;
          void 0 !== r && (document.title = r),
            jQuery(".am-helpdesk").on("keyup change", "textarea", function () {
              (this.style.overflow =
                this.scrollHeight < 400 ? "hidden" : "auto"),
                this.clientHeight < 400 &&
                  this.scrollHeight > this.clientHeight &&
                  (this.style.height =
                    Math.min(this.scrollHeight + 25, 400) + "px");
            }),
            t &&
              jQuery(".am-helpdesk").on(
                "submit",
                ".am-helpdesk-reply-panel-content form",
                function () {
                  var e = jQuery(this);
                  return (
                    e.ajaxSubmit({
                      cache: !1,
                      success: function () {
                        jQuery(
                          ".am-helpdesk-message-action-reply, .am-helpdesk-ticket-action-reply"
                        ).removeClass("am-helpdesk-action-active"),
                          e.closest(".am-helpdesk-reply-panel").hide(),
                          e.closest(".am-helpdesk-reply-panel-content").empty();
                      },
                    }),
                    !1
                  );
                }
              ),
            jQuery(".am-helpdesk-history-title-action a").click(function () {
              jQuery(".am-helpdesk-history-content").slideToggle();
            }),
            jQuery(document).on(
              "click",
              ".am-helpdesk-ticket-action-reply a, .am-helpdesk-message-action-reply a",
              function () {
                jQuery(
                  ".am-helpdesk-ticket-action-reply, .am-helpdesk-message-action-reply"
                ).removeClass("am-helpdesk-action-active"),
                  jQuery(this)
                    .closest(
                      ".am-helpdesk-ticket-action, .am-helpdesk-message-action"
                    )
                    .addClass("am-helpdesk-action-active");
                var e = jQuery(this)
                    .closest(
                      ".am-helpdesk-ticket-actions, .am-helpdesk-message-actions"
                    )
                    .siblings(".am-helpdesk-reply-panel"),
                  t = e.find(".am-helpdesk-reply-panel-content"),
                  a = "";
                return (
                  "" !== t.html() && (a = t.find("textarea").attr("value")),
                  jQuery(".am-helpdesk-reply-panel").hide(),
                  jQuery(".am-helpdesk-reply-panel-content").empty(),
                  t
                    .load(jQuery(this).attr("href"), function () {
                      a && jQuery(this).find("textarea").attr("value", a),
                        e.show(),
                        jQuery(this).find("textarea").focus();
                    })
                    .append(
                      "data:image/svg+xml;base64,PHN2ZyBjbGFzcz0ic3Bpbm5lciIgd2lkdGg9IjY1cHgiIGhlaWdodD0iNjVweCIgdmlld0JveD0iMCAwIDY2IDY2IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogICA8c3R5bGU+Ci5zcGlubmVyIHsKICAtd2Via2l0LWFuaW1hdGlvbjogcm90YXRvciAxLjRzIGxpbmVhciBpbmZpbml0ZTsKICAgICAgICAgIGFuaW1hdGlvbjogcm90YXRvciAxLjRzIGxpbmVhciBpbmZpbml0ZTsKfQoKQC13ZWJraXQta2V5ZnJhbWVzIHJvdGF0b3IgewogIDAlIHsKICAgIC13ZWJraXQtdHJhbnNmb3JtOiByb3RhdGUoMGRlZyk7CiAgICAgICAgICAgIHRyYW5zZm9ybTogcm90YXRlKDBkZWcpOwogIH0KICAxMDAlIHsKICAgIC13ZWJraXQtdHJhbnNmb3JtOiByb3RhdGUoMjcwZGVnKTsKICAgICAgICAgICAgdHJhbnNmb3JtOiByb3RhdGUoMjcwZGVnKTsKICB9Cn0KCkBrZXlmcmFtZXMgcm90YXRvciB7CiAgMCUgewogICAgLXdlYmtpdC10cmFuc2Zvcm06IHJvdGF0ZSgwZGVnKTsKICAgICAgICAgICAgdHJhbnNmb3JtOiByb3RhdGUoMGRlZyk7CiAgfQogIDEwMCUgewogICAgLXdlYmtpdC10cmFuc2Zvcm06IHJvdGF0ZSgyNzBkZWcpOwogICAgICAgICAgICB0cmFuc2Zvcm06IHJvdGF0ZSgyNzBkZWcpOwogIH0KfQoucGF0aCB7CiAgc3Ryb2tlLWRhc2hhcnJheTogMTg3OwogIHN0cm9rZS1kYXNob2Zmc2V0OiAwOwogIC13ZWJraXQtdHJhbnNmb3JtLW9yaWdpbjogY2VudGVyOwogICAgICAgICAgdHJhbnNmb3JtLW9yaWdpbjogY2VudGVyOwogIC13ZWJraXQtYW5pbWF0aW9uOiBkYXNoIDEuNHMgZWFzZS1pbi1vdXQgaW5maW5pdGU7CiAgICAgICAgICBhbmltYXRpb246IGRhc2ggMS40cyBlYXNlLWluLW91dCBpbmZpbml0ZTsKfQoKQC13ZWJraXQta2V5ZnJhbWVzIGRhc2ggewogIDAlIHsKICAgIHN0cm9rZS1kYXNob2Zmc2V0OiAxODc7CiAgfQogIDUwJSB7CiAgICBzdHJva2UtZGFzaG9mZnNldDogNDYuNzU7CiAgICAtd2Via2l0LXRyYW5zZm9ybTogcm90YXRlKDEzNWRlZyk7CiAgICAgICAgICAgIHRyYW5zZm9ybTogcm90YXRlKDEzNWRlZyk7CiAgfQogIDEwMCUgewogICAgc3Ryb2tlLWRhc2hvZmZzZXQ6IDE4NzsKICAgIC13ZWJraXQtdHJhbnNmb3JtOiByb3RhdGUoNDUwZGVnKTsKICAgICAgICAgICAgdHJhbnNmb3JtOiByb3RhdGUoNDUwZGVnKTsKICB9Cn0KQGtleWZyYW1lcyBkYXNoIHsKICAwJSB7CiAgICBzdHJva2UtZGFzaG9mZnNldDogMTg3OwogIH0KICA1MCUgewogICAgc3Ryb2tlLWRhc2hvZmZzZXQ6IDQ2Ljc1OwogICAgLXdlYmtpdC10cmFuc2Zvcm06IHJvdGF0ZSgxMzVkZWcpOwogICAgICAgICAgICB0cmFuc2Zvcm06IHJvdGF0ZSgxMzVkZWcpOwogIH0KICAxMDAlIHsKICAgIHN0cm9rZS1kYXNob2Zmc2V0OiAxODc7CiAgICAtd2Via2l0LXRyYW5zZm9ybTogcm90YXRlKDQ1MGRlZyk7CiAgICAgICAgICAgIHRyYW5zZm9ybTogcm90YXRlKDQ1MGRlZyk7CiAgfQp9Cjwvc3R5bGU+CiAgIDxjaXJjbGUgY2xhc3M9InBhdGgiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzMyMzIzMiIgc3Ryb2tlLXdpZHRoPSI2IiBzdHJva2UtbGluZWNhcD0icm91bmQiIGN4PSIzMyIgY3k9IjMzIiByPSIzMCI+PC9jaXJjbGU+Cjwvc3ZnPgo="
                    )
                    .show(),
                  !1
                );
              }
            ),
            jQuery(".am-helpdesk-ticket-status input[type=submit]").hide(),
            jQuery(".am-helpdesk-ticket-status select").change(function () {
              this.form.submit();
            }),
            jQuery(document).on(
              "click",
              ".am-helpdesk input[name=discard]",
              function () {
                jQuery(
                  ".am-helpdesk-message-action-reply, .am-helpdesk-ticket-action-reply"
                ).removeClass("am-helpdesk-action-active"),
                  jQuery(this).closest(".am-helpdesk-reply-panel").hide(),
                  jQuery(this)
                    .closest(".am-helpdesk-reply-panel-content")
                    .empty();
              }
            ),
            jQuery(document).on(
              "click",
              ".am-helpdesk-message-action-fold a",
              function () {
                var e = jQuery(this)
                    .closest(".am-helpdesk-message")
                    .find(".am-helpdesk-message-content"),
                  t = jQuery(this).closest(".am-helpdesk-message");
                "none" === e.css("display")
                  ? (jQuery(this).html("&minus;"),
                    t.removeClass("am-helpdesk-message-closed"),
                    e.show())
                  : (jQuery(this).html("+"),
                    e.hide(),
                    t.addClass("am-helpdesk-message-closed"));
              }
            ),
            jQuery(".am-helpdesk-message-action-fold a")
              .slice(3)
              .each(function (e, t) {
                jQuery(t).click();
              }),
            jQuery(document).on(
              "click",
              ".am-helpdesk-ticket-action-fold-all a",
              function () {
                var e = jQuery(".am-helpdesk-message-content");
                jQuery(this).hasClass("closed")
                  ? (jQuery(this).removeClass("closed"),
                    jQuery(".am-helpdesk-message-action-fold a").html(
                      "&minus;"
                    ),
                    jQuery(this).html("&minus;"),
                    e.show(),
                    jQuery(".am-helpdesk-message").removeClass(
                      "am-helpdesk-message-closed"
                    ))
                  : (jQuery(this).addClass("closed"),
                    jQuery(".am-helpdesk-message-action-fold a").html("+"),
                    jQuery(this).html("+"),
                    e.hide(),
                    jQuery(".am-helpdesk-message").addClass(
                      "am-helpdesk-message-closed"
                    ));
              }
            ),
            jQuery(document).on(
              "click",
              ".am-helpdesk-reply-panel-tool-emoji-item",
              function () {
                var e = jQuery(this)
                  .closest(".am-helpdesk-reply-panel")
                  .find("textarea[name=content]");
                e.insertAtCaret(this.innerHTML), e.change();
              }
            ),
            jQuery(document).on("submit", ".am-helpdesk-form", function () {
              jQuery(this)
                .find("input[type=submit], input[type=button]")
                .attr("disabled", "disabled");
            }),
            setInterval(function () {
              jQuery(".am-helpdesk-message-date").each(function () {
                jQuery(this)
                  .empty()
                  .append(
                    (function (e) {
                      var t,
                        a = e.getTime(),
                        n = (new Date().getTime() - a) / 1e3;
                      if (n >= 0 && n <= 59) return (0, i.S)("just now");
                      if (n >= 60 && n <= 3599)
                        t = Math.floor(n / 60) + " " + (0, i.S)("min");
                      else if (n >= 3600 && n <= 86399)
                        t = Math.floor(n / 3600) + " " + (0, i.S)("hrs");
                      else if (n >= 86400 && n < 2592e3)
                        t = Math.floor(n / 86400) + " " + (0, i.S)("days");
                      else if (n >= 2592e3 && n < 31104e3) {
                        var o = Math.floor(n / 2592e3);
                        t =
                          (0, i.S)("more than") +
                          " " +
                          o +
                          " " +
                          (0, i.S)("month");
                      } else {
                        var r = Math.floor(n / 31104e3);
                        t =
                          (0, i.S)("more than") +
                          " " +
                          r +
                          " " +
                          (0, i.S)("year");
                      }
                      return t + " " + (0, i.S)("ago");
                    })(new Date(jQuery(this).attr("datetime")))
                  );
              });
            }, 6e4),
            t && a && o(a);
        }
        (window.amHelpdeskUpdate = o),
          (0, a(3100).Z)(function () {
            r(),
              (function () {
                var e = (0, n.amVars)(),
                  t = e.ticketMask,
                  a = (e.isLive, e.adminAssignRedirectUrl),
                  i = e.adminEditCategoryRedirectUrl,
                  o = e.setTicketLock,
                  r = t,
                  l = amUrl("/helpdesk/admin/p/view/displaysnippets"),
                  s = amUrl("/helpdesk/admin/p/view/displayfaq"),
                  c = amUrl("/helpdesk/admin/p/view/displayassign"),
                  d = amUrl("/helpdesk/admin/p/view/displayeditcategory"),
                  u = amUrl("/helpdesk/admin/p/view/editwatcher");
                if (o) {
                  var p = function () {
                    return jQuery.post(o);
                  };
                  p(), setInterval(p, 6e4);
                }
                jQuery(document).on(
                  "click",
                  ".am-helpdesk-reply-panel-tool-snippets a",
                  function () {
                    var e = jQuery(this),
                      t = jQuery("<div></div>");
                    t.load(l, { ticket: r }, function () {
                      t.dialog({
                        autoOpen: !0,
                        modal: !0,
                        title: "Snippets",
                        width: 700,
                        position: { my: "center", at: "center", of: window },
                      }),
                        t.find(".am-grid-wrap").on("load", function () {
                          jQuery(this)
                            .find("a.am-helpdesk-insert-snippet")
                            .off()
                            .click(function () {
                              var a = e
                                .closest(".am-helpdesk-reply-panel")
                                .find("textarea[name=content]");
                              a.insertAtCaret(
                                jQuery(this).data("snippet-content")
                              ),
                                a.change(),
                                t.dialog("close");
                            });
                        });
                    });
                  }
                ),
                  jQuery(document).on(
                    "click",
                    ".am-helpdesk-reply-panel-tool-faq a",
                    function () {
                      var e = jQuery(this),
                        t = jQuery("<div></div>");
                      t.load(s, {}, function () {
                        t.dialog({
                          autoOpen: !0,
                          modal: !0,
                          title: "FAQ",
                          width: 700,
                          position: { my: "center", at: "center", of: window },
                        }),
                          t.find(".am-grid-wrap").on("load", function () {
                            jQuery(this)
                              .find("a.am-helpdesk-insert-faq")
                              .off()
                              .click(function () {
                                var a = e
                                  .closest(".am-helpdesk-reply-panel")
                                  .find("textarea[name=content]");
                                a.insertAtCaret(
                                  jQuery(this).data("faq-content")
                                ),
                                  a.change(),
                                  t.dialog("close");
                              });
                          });
                      });
                    }
                  ),
                  jQuery(document).on(
                    "click",
                    ".am-helpdesk-reply-panel-tool-markdown a",
                    function () {
                      jQuery(this);
                      var e = jQuery("<div></div>");
                      e.load(
                        amUrl("/helpdesk/admin/p/view/displaymarkdown"),
                        {},
                        function () {
                          e.dialog({
                            autoOpen: !0,
                            modal: !0,
                            title: "Markdown",
                            width: 700,
                            position: {
                              my: "center",
                              at: "center",
                              of: window,
                            },
                          });
                        }
                      );
                    }
                  ),
                  jQuery(document).on(
                    "click",
                    ".am-helpdesk-ticket-action-assign a",
                    function () {
                      var e = jQuery("<div></div>");
                      e.load(c, {}, function () {
                        e.dialog({
                          autoOpen: !0,
                          modal: !0,
                          title: "",
                          width: 700,
                          position: { my: "center", at: "center", of: window },
                        }),
                          e.find(".am-grid-wrap").on("load", function () {
                            jQuery(this)
                              .find("a.am-helpdesk-assign")
                              .off()
                              .click(function () {
                                window.location =
                                  a + jQuery(this).data("admin_id");
                              });
                          });
                      });
                    }
                  ),
                  jQuery(document).on(
                    "click",
                    ".am-helpdesk-ticket-action-change-category a",
                    function () {
                      var e = jQuery("<div></div>");
                      e.load(d, {}, function () {
                        e.dialog({
                          autoOpen: !0,
                          modal: !0,
                          title: "",
                          width: 700,
                          position: { my: "center", at: "center", of: window },
                        }),
                          e.find(".am-grid-wrap").on("load", function () {
                            jQuery(this)
                              .find("a.am-helpdesk-edit-category")
                              .off()
                              .click(function () {
                                window.location =
                                  i + jQuery(this).data("category_id");
                              });
                          });
                      });
                    }
                  ),
                  jQuery(document).on(
                    "click",
                    ".am-helpdesk-ticket-action-change-watcher a",
                    function () {
                      var e = jQuery("<div></div>");
                      e.load(u, { ticket: t }, function () {
                        e.dialog({
                          autoOpen: !0,
                          modal: !0,
                          title: "",
                          width: 700,
                          position: { my: "center", at: "center", of: window },
                        });
                      });
                    }
                  );
              })(),
              jQuery(document).on("keyup", "#am-helpdesk-faq-q", function () {
                if (!(jQuery(this).val().length <= 3))
                  return (
                    jQuery.get(
                      jQuery(this).closest("form").attr("action"),
                      { q: jQuery(this).val() },
                      function (e) {
                        jQuery("#am-helpdesk-faq-q-result").empty().append(e);
                      }
                    ),
                    !1
                  );
                jQuery("#am-helpdesk-faq-q-result").empty();
              }),
              jQuery("#helpdesk-search").on("keyup keypress", function (e) {
                if (13 == (e.keyCode || e.which)) return e.preventDefault(), !1;
              });
          });
      },
      77086: function (e, t, a) {
        "use strict";
        a.r(t);
        a(69826), a(41539), a(69600), a(40561), a(74916), a(23123), a(47042);
        !(function (e) {
          e.fn.adminMenu = function (t) {
            return this.each(function () {
              var a,
                n = e(this),
                i = "am-menu";
              function o(e) {
                e = l(e);
                for (var t = r(), a = 0; a < t.length; a++)
                  if (t[a] == e) return !0;
                return !1;
              }
              function r() {
                var e = getCookie(i);
                return e ? e.split(";") : [];
              }
              function l(e) {
                return e.slice(5);
              }
              t &&
                !n.data("selected") &&
                (n.data("selected", 1),
                (a = "menu-" + (a = t)),
                e("li.active", n)
                  .not("#" + a)
                  .removeClass("active"),
                e("#" + a, n)
                  .parents("li", n)
                  .addClass("active")),
                n.data("initialized") ||
                  (n.data("initialized", 1),
                  e("a.folder", n).click(function () {
                    var t = e(this).attr("id");
                    return (
                      o(t)
                        ? (e(this).closest("li").find("ul").slideUp("slow"),
                          (function (e) {
                            e = l(e);
                            for (var t = r(), a = 0; a < t.length; a++)
                              if (t[a] == e) {
                                t.splice(a, 1);
                                break;
                              }
                            setCookie(i, t.join(";"));
                          })(t),
                          e(this)
                            .closest("li")
                            .removeClass("opened")
                            .addClass("closed"))
                        : (e(this).closest("li").find("ul").slideDown("slow"),
                          (function (e) {
                            e = l(e);
                            var t = r();
                            o(e) || t.push(e);
                            setCookie(i, t.join(";"));
                          })(t),
                          e(this)
                            .closest("li")
                            .removeClass("closed")
                            .addClass("opened")),
                      !1
                    );
                  }));
            });
          };
        })(jQuery);
      },
      71059: function (e, t, a) {
        "use strict";
        a.r(t);
        a(69826), a(41539), a(68309), a(2707);
        !(function (e) {
          e(document).on(
            "keyup change",
            ".dir-browser .input-storage-filter",
            function () {
              e(".dir-browser .input-storage-filter-empty").toggle(
                0 != this.value.length
              );
              var t = jQuery(".dir-browser");
              this.value.length
                ? (e(".dir-browser-item", t).hide(),
                  e(
                    '.dir-browser-item[data-title*="' +
                      this.value.toLowerCase() +
                      '"]',
                    t
                  ).show())
                : e(".dir-browser-item", t).show();
            }
          ),
            e(document).on(
              "click",
              ".dir-browser .input-storage-filter-empty",
              function () {
                e(this)
                  .closest(".dir-browser .input-storage-filter-wrapper")
                  .find(".input-storage-filter")
                  .val("")
                  .change();
              }
            ),
            (e.fn.dirBrowser = function (t) {
              return this.each(function () {
                var a = this;
                if (!e(a).data("initialized")) {
                  if ("text" !== this.type)
                    throw new Error(
                      "Element should be input-text to use browser for it"
                    );
                  e(a).data("initialized", 1);
                  var n = e.extend(
                      {
                        urlField: "",
                        browserController: "admin-dirbrowser",
                        dialogTitle: "Directory Browser",
                      },
                      t
                    ),
                    i = new Array(),
                    o = "asc",
                    r = null,
                    l = e("<div></div>").hide();
                  e(a).after(l);
                  var s = e('<a class="local">browse&hellip;</a>').attr(
                    "href",
                    "javascript:;"
                  );
                  e(a).after(s),
                    s.before(" "),
                    s.click(function () {
                      l.dialog({
                        modal: !0,
                        title: n.dialogTitle,
                        width: 600,
                        height: 500,
                        position: { my: "center", at: "center", of: window },
                        buttons: {
                          Cancel: function () {
                            e(this).dialog("close");
                          },
                        },
                        open: function () {
                          e(this)
                            .closest(".ui-dialog")
                            .find(".ui-dialog-buttonpane")
                            .prepend(
                              '<div style="float:left; padding:1em; font-style:italic" class="am-popup-footer-note">                                     Click radio-button to choose a directory                                </div>'
                            ),
                            c(e(a).val(), !0);
                        },
                        close: function () {
                          e(this)
                            .closest(".ui-dialog")
                            .find(".am-popup-footer-note")
                            .remove(),
                            l.empty();
                        },
                      });
                    });
                }
                function c(t, a) {
                  var o = setTimeout(function () {
                      l.html(
                        '<div style="padding:2em; text-align:center"><img src="' +
                          amUrl(
                            "/application/default/views/public/img/loading-b.svg"
                          ) +
                          '" width="28" height="28"></div>'
                      );
                    }, 500),
                    r = {};
                  t && ((r.dir = t), a && (r.selected = a)),
                    e.get(amUrl("/" + n.browserController), r, function (e, t) {
                      clearTimeout(o),
                        (i = JSON.parse(e)),
                        l.empty().append(d(i));
                    });
                }
                function d(t) {
                  return e("<div></div>")
                    .append(
                      (function (t, a) {
                        var n,
                          i = e("<div></div>").addClass("path"),
                          o = e('<a class="local"></a>').attr(
                            "href",
                            "javascript:;"
                          );
                        for (var r in t)
                          r > 0 &&
                            i.append(
                              ' <span class="path-separator">' + a + "</span> "
                            ),
                            (n = t[r].path
                              ? o
                                  .clone()
                                  .append(t[r].name)
                                  .data("path", t[r].path)
                                  .click(function () {
                                    c(e(this).data("path"));
                                  })
                              : e(document.createTextNode(t[r].name))),
                            i.append(n);
                        return i;
                      })(t.currentDir, t.separator)
                    )
                    .append(
                      e(
                        '<div class="input-storage-filter-wrapper">    <div class="input-storage-filter-inner-wrapper">        <input class="input-storage-filter"               type="text"               name="q"               autocomplete="off"               placeholder="type part of file name to filter…" />        <div class="input-storage-filter-empty">&nbsp;</div>    </div></div>'
                      )
                    )
                    .append(
                      (function (t, i) {
                        var s,
                          d = e(
                            '<table class="am-grid grid-no-highlight"></table>'
                          ).css({ overflow: "auto" }),
                          p = e("<tr></tr>"),
                          f = e("<th></th>"),
                          h = e("<td></td>"),
                          m = e("<input></input>").attr({
                            name: "___browser___",
                            type: "radio",
                          }),
                          g = e('<a class="local"></a>').attr({
                            href: "javascript:;",
                          });
                        d.append(
                          p
                            .clone()
                            .append(f.clone())
                            .append(f.clone().append(u("Name", "name", !0)))
                            .append(f.clone().append(u("Mode", "perm", !1)))
                            .append(
                              f.clone().append(u("Created", "created", !0))
                            )
                        ),
                          i &&
                            ((s = e(
                              document.createTextNode(
                                i.name
                                  ? "Previous Directory (" + i.name + ")"
                                  : "Root"
                              )
                            )),
                            i.path &&
                              (s = g
                                .clone()
                                .append(s.clone())
                                .click(function () {
                                  c(e(this).closest("tr").data("path"));
                                })),
                            d.append(
                              p
                                .clone()
                                .addClass("am-grid-row")
                                .data("path", i.path)
                                .append(h.clone().attr("colspan", 4).append(s))
                            ));
                        null !== r &&
                          t.sort(function (e, t) {
                            return e[r] > t[r]
                              ? "asc" === o
                                ? 1
                                : -1
                              : e[r] < t[r]
                              ? "asc" === o
                                ? -1
                                : 1
                              : 0;
                          });
                        for (var v in t) {
                          var y = m.clone().click(function () {
                            if (
                              (e(a).val(e(this).closest("tr").data("path")),
                              e(a).change(),
                              n.urlField)
                            ) {
                              var t = e(this).closest("tr").data("url");
                              t
                                ? e(n.urlField).val(t).addClass("disabled")
                                : e(n.urlField).val("").removeClass("disabled");
                            }
                            l.dialog("close");
                          });
                          t[v].selected && y.prop("checked", !0),
                            d.append(
                              p
                                .clone()
                                .addClass("am-grid-row dir-browser-item")
                                .attr("data-title", t[v].name.toLowerCase())
                                .data("path", t[v].path)
                                .data("url", t[v].url)
                                .append(h.clone().attr("width", "1%").append(y))
                                .append(
                                  h
                                    .clone()
                                    .append(g.clone().append(t[v].name))
                                    .click(function () {
                                      c(e(this).closest("tr").data("path"));
                                    })
                                )
                                .append(h.clone().append(t[v].perm))
                                .append(h.clone().append(t[v].created))
                                .data("title", t[v].name)
                            );
                        }
                        return e(
                          '<div class="am-grid-container am-grid-storage"></div>'
                        ).append(d);
                      })(t.dirList, t.prevDir)
                    )
                    .addClass("dir-browser");
                }
                function u(t, a, n) {
                  if (n) {
                    var s = e("<a></a>")
                      .attr({ href: "javascript:;" })
                      .append(t)
                      .addClass("a-sort")
                      .data("name", a);
                    r === a &&
                      (s.addClass("sorted-" + o), s.data("sortDir", o)),
                      s.click(function () {
                        "asc" === e(this).data("sortDir")
                          ? (e(this).data("sortDir", "desc"), (o = "desc"))
                          : (e(this).data("sortDir", "asc"), (o = "asc")),
                          (r = e(this).data("name")),
                          l.empty().append(d(i));
                      });
                  } else s = t;
                  return s;
                }
              });
            });
        })(jQuery);
      },
      98009: function (e, t, a) {
        "use strict";
        a.r(t);
        a(74916), a(23123), a(69826), a(41539);
        !(function (e) {
          e.fn.fileStyle = function () {
            return this.each(function () {
              var n = e(this);
              if (!n.data("initialized")) {
                if ("file" != this.type)
                  throw new Error(
                    "Element should be input-text to use browser for it"
                  );
                n.data("initialized", 1);
                var i = e(
                  '<div class="input-file">                     <div class="input-file-button">' +
                    am_i18n.file_style_browse +
                    '</div>                     <div class="input-file-input"></div>                   </div> '
                );
                n.before(i),
                  n.change(function () {
                    if (this.files.length > 1)
                      t = "".concat(this.files.length, " files");
                    else {
                      var t = e(this).val().split("\\").pop();
                      t.length > 20 && (t = "..." + t.substr(-20));
                    }
                    i.find(".input-file-input").empty().append(t);
                  });
                var o = n
                  .wrap(e("<div />"))
                  .parent()
                  .css({ overflow: "hidden", width: "20px", height: "20px" })
                  .css({ position: "absolute", "z-index": 1e3 });
                i.append(o),
                  n.css({ float: "right" }),
                  o.css({ opacity: 0 }),
                  i.on("mouseover mouseout", function () {
                    e(this).toggleClass("hover");
                  }),
                  i.mousemove(function (e) {
                    o.offset({ top: a(e, i), left: t(e, i) });
                  });
              }
            });
            function t(e, t) {
              var a = e.pageX - 10;
              return (
                a > t.offset().left + t.outerWidth() - 10 &&
                  (a = t.offset().left + t.outerWidth() - 20),
                a < t.offset().left && (a = t.offset().left),
                a
              );
            }
            function a(e, t) {
              var a = e.pageY - 10;
              return (
                a > t.offset().top + t.outerHeight() - 10 &&
                  (a = t.offset().top + t.outerHeight() - 20),
                a < t.offset().top && (a = t.offset().top),
                a
              );
            }
          };
        })(jQuery);
      },
      96487: function () {
        jQuery.fn.insertAtCaret = function (e) {
          return this.each(function () {
            if (document.selection)
              this.focus(),
                (sel = document.selection.createRange()),
                (sel.text = e),
                this.focus();
            else if (this.selectionStart || "0" == this.selectionStart) {
              var t = this.selectionStart,
                a = this.selectionEnd,
                n = this.scrollTop;
              (this.value =
                this.value.substring(0, t) +
                e +
                this.value.substring(a, this.value.length)),
                this.focus(),
                (this.selectionStart = t + e.length),
                (this.selectionEnd = t + e.length),
                (this.scrollTop = n);
            } else (this.value += e), this.focus();
          });
        };
      },
      67515: function (e, t, a) {
        "use strict";
        a.r(t);
        a(40561);
        /**
         * jQuery custom event "outerClick".
         * @author David Brockman Smoliansky http://littleroom.se/
         * @license GNU Lesser General Public License: http://creativecommons.org/licenses/LGPL/2.1/
         * @version 1.1
         * 2009/02/27
         *
         * The outerClick event is fired when an element outside of the target element is clicked.
         *
         * Usage:
         * $(selector).on("outerClick", fn);   // Bind the function fn to the outerClick event on each of the matched elements.
         * $(selector).outerClick(fn);           // Bind the function fn to the outerClick event on each of the matched elements.
         * $(selector).trigger("outerClick");    // Trigger the outerClick event on each of the matched elements.
         * $(selector).outerClick();             // Trigger the outerClick event on each of the matched elements.
         * $(selector).off("outerClick", fn); // Unbind the function fn from the outerClick event on each of the matched elements.
         * $(selector).off("outerClick");     // Unbind all outerClick events from each of the matched elements.
         */
        !(function (e, t, a) {
          function n(n) {
            for (var i, o = 0, r = t.length, l = n.target; o < r; o++)
              (i = t[o]) === l ||
                (i.contains
                  ? i.contains(l)
                  : !i.compareDocumentPosition ||
                    16 & i.compareDocumentPosition(l)) ||
                e.event.trigger(a, n, i);
          }
          (e.event.special.outerClick = {
            setup: function () {
              var a = t.length;
              a || e.event.add(document, "click", n),
                e.inArray(this, t) < 0 && (t[a] = this);
            },
            teardown: function () {
              var a = e.inArray(this, t);
              a >= 0 &&
                (t.splice(a, 1),
                t.length || e.event.remove(document, "click", n));
            },
          }),
            (e.fn.outerClick = function (e) {
              return e ? this.on(a, e) : this.trigger(a);
            });
        })(jQuery, [], "outerClick");
      },
      36747: function (e, t, a) {
        "use strict";
        a.r(t);
        a(2707), a(69826), a(41539);
        !(function (e) {
          (e.valHooks.__magic_select_saved = e.valHooks.select),
            (e.valHooks.select = {
              get: function (t, a) {
                return e(t).hasClass("magicselect")
                  ? t._getMagicValue()
                  : e.valHooks.__magic_select_saved.get(t, a);
              },
              set: function (t, a) {
                if (!e(t).hasClass("magicselect"))
                  return e.valHooks.__magic_select_saved.set(t, a);
              },
            }),
            (e.fn.magicSelect = function (t) {
              return this.each(function () {
                var a = this;
                if (!e(a).data("initialized")) {
                  if ("select-multiple" !== this.type)
                    throw new Error(
                      "Element should be multiselect to use magicselect for it"
                    );
                  e(a).data("initialized", 1),
                    e(a).attr("data-orig-params", JSON.stringify(t || {})),
                    (a._getMagicValue = function () {
                      var t = e(this.parentNode),
                        a = [];
                      return (
                        e(".magicselect-item input[type=hidden]", t).each(
                          function () {
                            a.push(this.value);
                          }
                        ),
                        a
                      );
                    }),
                    (a._setMagicValue = function (e) {});
                  var n = e(a).data("offer") || am_i18n.ms_please_select,
                    i = e.extend(
                      {
                        selectOffer: n,
                        selectAllOffer: am_i18n.ms_select_all,
                        allowSelectAll: e(a).data("select-all") || !1,
                        allowSameValue: !1,
                        getOptionName: function (e, t) {
                          return e;
                        },
                        getOptionValue: function (t) {
                          return e(t).val();
                        },
                        onOptionAdded: function (e, t) {},
                        deleteTitle: "&#10005;",
                        onChange: function (e) {},
                        callbackTitle: function (t) {
                          return e(t).data("label")
                            ? e(t).data("label")
                            : e("<div></div>").text(t.text).html();
                        },
                      },
                      t
                    ),
                    o = new Object();
                  e(a).wrap("<div></div>"),
                    i.sortable && e(a).parent().sortable({ items: "div" }),
                    (a.update = function (t) {
                      e(a).empty();
                      var n = e("<option></option>");
                      e(a).append(
                        n.clone().append(i.selectOffer).val("__special__offer")
                      ),
                        e.each(t, function (t, i) {
                          e(a).append(n.clone().attr("value", t).append(i));
                        }),
                        e(a).nextAll("[class^=magicselect-item]").remove(),
                        e.each(o, function () {
                          var t = e("option[value=" + this + "]", e(a));
                          t.get(0) && u(t.get(0), !0);
                        }),
                        d();
                    }),
                    e(a).prop("size", 1),
                    e(a).data("name", e(a).attr("name")),
                    e(a).attr("data-name", e(a).attr("name")),
                    e(a).prepend(
                      e('<option value="__special__offer"></option>').append(
                        i.selectOffer
                      )
                    );
                  var r = [];
                  e.each(this.options, function () {
                    r.push(this);
                  }),
                    i.sortable &&
                      r.sort(function (t, a) {
                        return parseInt(e(t).data("sort_order")) <
                          parseInt(e(a).data("sort_order"))
                          ? 1
                          : parseInt(e(t).data("sort_order")) ===
                            parseInt(e(a).data("sort_order"))
                          ? 0
                          : -1;
                      });
                  var l = e(a).data("value"),
                    s = {};
                  if (
                    (l
                      ? e.each(l, function () {
                          (!i.allowSameValue && s.hasOwnProperty(this)) ||
                            ((s[this] = !0),
                            e(a)
                              .find('option[value="' + this + '"]')
                              .get(0) &&
                              u(
                                e(a)
                                  .find('option[value="' + this + '"]')
                                  .get(0),
                                !0
                              ));
                        })
                      : e.each(r, function () {
                          this.selected && u(this, !0);
                        }),
                    i.onChange.call(e(a), o),
                    e(a).prop("multiple", !1),
                    e(a).prop("name", "_" + e(a).prop("name")),
                    (a.selectedIndex = null),
                    i.allowSelectAll)
                  ) {
                    var c = e(
                      '<a href="javascript:;" class="local"></a>'
                    ).append(i.selectAllOffer);
                    e(a).after(c),
                      c.before(" "),
                      c.click(function () {
                        e(a)
                          .find("option")
                          .not("[value^=__]")
                          .not("[disabled]")
                          .each(function (e, t) {
                            u(t, !0);
                          }),
                          d();
                      });
                  }
                  e(a).change(function () {
                    var t = this.options[this.selectedIndex];
                    "__special__" != t.value.substring(0, 11) &&
                      (u(t), (a.selectedIndex = null), e(a).blur(), d());
                  });
                }
                function d() {
                  e(a).data("select2") &&
                    setTimeout(function () {
                      e(a).select2(e(a).data("select2-option"));
                    }, 0);
                }
                function u(t, n) {
                  if (t.selected || n) {
                    (o[t.value] = t.value),
                      i.allowSameValue || e(t).prop("disabled", !0);
                    var r = e(t),
                      l = e('<a href="javascript:;" class="am-link-del"></a>');
                    l.append(i.deleteTitle),
                      l.click(function () {
                        r.prop("disabled", !1),
                          delete o[r.val()],
                          i.onChange.call(e(a), o),
                          e(this).parent().remove(),
                          d();
                      });
                    var s = e('<input type="hidden" />');
                    s.prop("name", i.getOptionName(e(a).data("name"), t)),
                      s.prop("value", i.getOptionValue(t));
                    var c = e("<div></div>");
                    c.addClass(
                      i.sortable
                        ? "magicselect-item-sortable"
                        : "magicselect-item"
                    ),
                      c.append(l),
                      c.append(" "),
                      c.append(i.callbackTitle(t)),
                      c.append(s),
                      i.onOptionAdded(c, t),
                      e(a).parent().append(c),
                      n || i.onChange.call(e(a), o);
                  }
                }
              });
            });
        })(jQuery),
          (function (e) {
            e.fn.restoreMagicSelect = function () {
              return this.each(function () {
                var t = this,
                  a = e(t).attr("data-orig-params") || {},
                  n = e(t).attr("data-name"),
                  i = e(t).closest("div"),
                  o = i.find("select"),
                  r = e("<select></select>");
                e.each(
                  [
                    "id",
                    "data-offer",
                    "data-type",
                    "data-orig-params",
                    "class",
                  ],
                  function (e, t) {
                    r.attr(t, o.attr(t));
                  }
                ),
                  r.attr({ name: n, multiple: "multiple" }),
                  o.children().each(function (t, a) {
                    switch (a.tagName) {
                      case "OPTION":
                        "__special__" !== a.value.substring(0, 11) &&
                          r.append(a);
                        break;
                      case "OPTGROUP":
                        r.append(a),
                          e(a)
                            .find("select")
                            .each(function (t, n) {
                              e(a).append(n);
                            });
                    }
                  });
                var l = [];
                i.find("input[type=hidden]").each(function (e, t) {
                  l.push(t.value);
                }),
                  r.data("value", l),
                  i.after(r),
                  i.remove(),
                  r.magicSelect(JSON.parse(a));
              });
            };
          })(jQuery);
      },
      51573: function (e, t, a) {
        "use strict";
        a.r(t);
        var n, i, o, r, l, s, c, d, u, p, f, h, m, g, v, y, b;
        a(74916),
          a(15306),
          a(4723),
          a(69826),
          a(41539),
          a(88674),
          a(54747),
          a(47941),
          a(47042),
          a(82526),
          a(41817),
          a(32165),
          a(66992),
          a(78783),
          a(33948),
          a(24603),
          a(39714),
          a(23123);
        window.HTMLReg =
          ((n = ""),
          (i = "http://www.gmodules.com/ig/proxy?url="),
          (o = {}),
          (r = ""),
          (l = /^(?:input|br|hr|img|image)$/i),
          (s =
            /(?:canvas|form|optgroup|button|legend|fieldset|label|option|select|textarea|input|audio|aside|article|a|abbr|acronym|address|area|b|bdo|big|br|canvas|caption|center|cite|code|col|dd|del|dfn|dir|div|dl|dt|em|font|h[1-6]|hr|i|img|ins|kbd|li|map|ol|p|pre|q|s|samp|small|span|strike|strong|sub|sup|table|tbody|td|tfoot|th|thead|tr|tt|u|ul|blockquote|image|video|xmp)/),
          (c =
            /(?:type|accesskey|align|alink|alt|background|bgcolor|border|cellpadding|cellspacing|class|color|cols|colspan|coords|dir|face|height|href|hspace|id|ismap|lang|marginheight|marginwidth|multiple|name|nohref|noresize|noshade|nowrap|ref|rel|rev|rows|rowspan|scrolling|shape|span|src|style|summary|tabindex|target|title|usemap|valign|value|vlink|vspace|width)/),
          (d = new RegExp(
            "(?:\"[^\"]{0,1000}\"|[^\\s'\"`>]{1,1000}|'[^']{0,1000}')"
          )),
          (u = new RegExp(
            '(?:"[^"]{0,1000}"|[^\\s>]{1,1000}|\'[^>]{0,1000}\')'
          )),
          (p = new RegExp("\\s+" + c.source + "\\s*=" + d.source)),
          (f = /^(?:https?:\/\/.+|\/.+|\w[^:]+|#[\w=?]*)$/),
          (h = new RegExp("[^<>]{1,1000}")),
          (m = new RegExp(
            "<[^>]+(?:(?:[\\s\\/]+\\w+\\s*=" + u.source + ")+)>"
          )),
          (g = new RegExp(
            "(" +
              /(?:<style>[^<>]+<\/style>)/.source +
              ")|(<\\/?[a-z0-9]{1,10}(?:" +
              p.source +
              "){0,20}(?:\\s*\\/?)>)|(" +
              h.source +
              ")|(" +
              m.source +
              ")",
            "ig"
          )),
          (v = function (e) {
            var t = document.createDocumentFragment();
            (t.innerHTML = e), (e = t.innerHTML);
            var a = new RegExp(
                "\\s+(?:sandbox-style|" + c.source + ")\\s*=" + d.source
              ),
              n = new RegExp(
                "(?:\\s" +
                  c.source +
                  "\\s*=" +
                  d.source +
                  ")|(?:\\s+(sandbox-style)\\s*=(" +
                  d.source +
                  "))",
                "gi"
              );
            return (
              (e = e.replace(
                new RegExp(
                  "(?:<[a-z0-9]{1,10}(?:" + a.source + "){0,20}(?:\\s*\\/?)>)",
                  "ig"
                ),
                function (e) {
                  return (e = e.replace(n, function (e, t, a) {
                    return void 0 !== t && t.length ? " style=" + a : e;
                  }));
                }
              )),
              (t = null),
              HTMLReg.validateHTML
                ? (function (e) {
                    try {
                      if (window.DOMParser) {
                        var t = new DOMParser().parseFromString(e, "text/xml"),
                          a = new XMLSerializer().serializeToString(t);
                        return (
                          (a = a.replace(/^<\?[^?]+\?>\s*/, "")),
                          /<parsererror[^>]+>/.test(a)
                            ? "Invalid HTML markup"
                            : a
                        );
                      }
                      if (window.ActiveXObject) {
                        if (
                          (((t = new ActiveXObject("Microsoft.XMLDOM")).async =
                            "false"),
                          t.loadXML(e),
                          !t.xml)
                        )
                          throw {};
                        return t.xml;
                      }
                      return e;
                    } catch (e) {
                      return "Invalid HTML markup";
                    }
                  })(e)
                : e
            );
          }),
          (y = function (e, t) {
            return t
              ? "" === t[e]
                ? ""
                : f.test(t.getAttribute(e))
                ? t.getAttribute(e)
                : "#"
              : "";
          }),
          (b = function (e) {
            return (e = (e += "").replace(/[^\w ;&=\/():]/gi, function (e) {
              return "&#" + e.charCodeAt(0) + ";";
            }));
          }),
          {
            parse: function (e) {
              HTMLReg.disablePositioning
                ? (CSSReg.disablePositioning = !0)
                : (CSSReg.disablePositioning = !1);
              var t = "";
              return (
                (r = ""),
                e.replace(g, function (e, a, o, c, d) {
                  if (void 0 !== o && o.length) {
                    if (
                      !new RegExp("^<\\/?" + s.source + "/?[\\s>]", "i").test(o)
                    )
                      return "";
                    (r += "tag(" + o + ")\n"),
                      /^<\/?[a-z0-9]+>$/i.test(o) ||
                        (o = (function (e) {
                          var t = "";
                          if (
                            ((e = e.replace(
                              new RegExp(
                                "^(<\\/?)(" + s.source + ")(\\s|\\/)",
                                "i"
                              ),
                              function (e, a, n, i) {
                                return (t = n), a + "div" + i;
                              }
                            )),
                            "" === t)
                          )
                            return "";
                          var a = document.createElement("div"),
                            o = "";
                          (a.style.display = "none"), (a.innerHTML = e);
                          var r = a.firstChild;
                          if (!r) return "";
                          var c = y("href", r),
                            d = y("src", r),
                            u = y("background", r),
                            p = y("action", r);
                          if ("" !== r.id) {
                            var f = r.id + "";
                            f = f.replace(/[^\w]/g, "");
                            var h = n + "_" + f + "_";
                          } else h = "";
                          if ("" !== r.className) {
                            var m = r.className + "";
                            "" === (m = m.replace(/[^ \w]/g, "")) &&
                              (m = "invalid");
                            var g = "",
                              v = (m = m.split(" ")).length;
                            v > 10 && (v = 10);
                            for (var w = 0; w < v; w++)
                              /^[\w]+$/.test(m[w]) &&
                                (g += n + "_" + m[w] + "_ ");
                            g = g.replace(/\s$/, "");
                          } else g = "";
                          if (
                            "" !== r.getAttribute("name") &&
                            null !== r.getAttribute("name")
                          ) {
                            var j = r.getAttribute("name");
                            (j = j.replace(/[^\w]/g, "")),
                              r.setAttribute("name", "$" + n + "_" + j + "$");
                          }
                          if (
                            "" !== r.getAttribute("style") &&
                            null !== r.getAttribute("style") &&
                            "" !== r.style.cssText
                          ) {
                            var k = r.style.cssText;
                            if (
                              ((r.style.cssText = null),
                              r.setAttribute("style", ""),
                              r.removeAttribute("style"),
                              "" !== r.style.cssText)
                            )
                              return "";
                            CSSReg.setAppID(n),
                              (k = CSSReg.parse(k)),
                              r.setAttribute("sandbox-style", k);
                          } else
                            (r.style.cssText = null),
                              r.setAttribute("style", ""),
                              r.removeAttribute("style");
                          try {
                            /^a$/i.test(t) && r.setAttribute("rel", "nofollow"),
                              "" !== c &&
                                null != c &&
                                (/^#/.test(c)
                                  ? r.setAttribute("href", c)
                                  : r.setAttribute(
                                      "href",
                                      i + encodeURIComponent(c)
                                    )),
                              "" !== d &&
                                null != d &&
                                r.setAttribute(
                                  "src",
                                  i + encodeURIComponent(d)
                                ),
                              "" !== u &&
                                null != u &&
                                r.setAttribute(
                                  "background",
                                  i + encodeURIComponent(u)
                                ),
                              "" !== p &&
                                null != p &&
                                r.setAttribute(
                                  "action",
                                  i + encodeURIComponent(p)
                                ),
                              "" !== h && null != h && (r.id = h),
                              "" !== g && null != g && (r.className = g);
                          } catch (e) {}
                          for (
                            o += "<" + t, w = 0;
                            w < r.attributes.length;
                            w++
                          ) {
                            var Q = r.attributes[w].nodeValue;
                            null == Q ||
                              "" === Q ||
                              0 == Q ||
                              /contentEditable/i.test(
                                r.attributes[w].nodeName
                              ) ||
                              (o +=
                                " " +
                                r.attributes[w].nodeName +
                                '="' +
                                b(Q) +
                                '"');
                          }
                          return l.test(t) && (o += " /"), (a = null), o + ">";
                        })(o)),
                      (t += o);
                  } else
                    void 0 !== a && a.length
                      ? ((r += "styleTag(" + a + ")\n"),
                        (a = (function (e) {
                          var t = "<style>\n";
                          return (
                            e.replace(
                              /^<style>([^<>]+)<\/style>$/,
                              function (e, a) {
                                CSSReg.setAppID(n),
                                  (a = CSSReg.parse(a)),
                                  (t += a);
                              }
                            ),
                            (t += "\n</style>")
                          );
                        })(a)),
                        (t += a))
                      : void 0 !== c && c.length
                      ? ((t += c), (r += "text(" + c + ")\n"))
                      : void 0 !== d &&
                        d.length &&
                        (r += "invalidTags(" + d + ")\n");
                }),
                o.rawOutput && o.rawOutput(t),
                o.parseTree && o.parseTree(r),
                v(t)
              );
            },
            setAppID: function (e) {
              n = e;
            },
            setDebugObjs: function (e) {
              o = e;
            },
          });
        var w = window.HTMLReg;
        function j(e) {
          return (
            (j =
              "function" == typeof Symbol && "symbol" == typeof Symbol.iterator
                ? function (e) {
                    return typeof e;
                  }
                : function (e) {
                    return e &&
                      "function" == typeof Symbol &&
                      e.constructor === Symbol &&
                      e !== Symbol.prototype
                      ? "symbol"
                      : typeof e;
                  }),
            j(e)
          );
        }
        (window.CSSReg = (function e() {
          var t = "",
            a = {},
            n = "",
            i = new RegExp("(?:http:\\/{2}|\\/)(?:[^)]+)"),
            o = new RegExp(
              "(?:url[(](?:" +
                i.source +
                '|["]' +
                i.source +
                "[\"]|[']" +
                i.source +
                "['])[)])"
            ),
            r = new RegExp("\\s*:\\s*"),
            l = new RegExp(
              "((?:(?:[.#]\\w{1,20}|form|optgroup|button|legend|fieldset|label|option|select|textarea|input|audio|aside|article|a|abbr|acronym|address|area|b|bdo|big|br|canvas|caption|center|cite|code|col|dd|del|dfn|dir|div|dl|dt|em|font|h[1-6]|hr|i|img|ins|kbd|li|map|ol|p|pre|q|s|samp|small|span|strike|strong|sub|sup|table|tbody|td|tfoot|th|thead|tr|tt|u|ul|blockquote|image|video|xmp|[*])(?:[:](?:visited|link|hover|active|focus))?\\s*[,]?\\s*){1,10}[{])"
            ),
            s = new RegExp("([}])"),
            c = new RegExp(
              "(?:(?:normal|auto|(?:[+-]?[\\/.\\d]{1,8}\\s*){1,4}(?:px|%|pt|pc|em|mm|ex|in|cm)?))"
            ),
            d = new RegExp(
              "(?:(?:transparent|aqua|black|blue|fuchsia|gray|grey|green|lime|maroon|navy|olive|purple|red|silver|teal|white|yellow)|(?:rgb\\(\\s*\\d{1,3}%?\\s*,\\s*\\d{1,3}%?\\s*,\\s*\\d{1,3}%?\\))|(?:#(?:[0-9a-f]{6}|[0-9a-f]{3})))"
            ),
            u = new RegExp(
              "((?:(?:background-)?color" + r.source + ")" + d.source + ")"
            ),
            p = new RegExp(
              "((?:text-decoration" +
                r.source +
                ")(?:none|underline|overline|line-through|blink))"
            ),
            f = new RegExp(
              "((?:(?:position|whitespace|display|clear|float|(?:text|vertical)-align)" +
                r.source +
                ")(?:inherit|relative|static|absolute|normal|pre|nowrap|block|inline|list-item|both|none|left|right|center|justify|baseline|sub|super|top|text-top|middle|bottom|text-bottom|[+-]?\\d+%))"
            ),
            h = new RegExp(
              "((?:(?:line-height|text-ident|letter-spacing|word-spacing|width|height|top|left|right|bottom|margin(?:-(?:left|right|top|bottom))?|padding(?:-(?:left|right|top|bottom))?)" +
                r.source +
                ")" +
                c.source +
                ")"
            ),
            m = new RegExp(
              "(?:" +
                c.source +
                "|serif|arial|[\"]lucida console[\"]|[']lucida console[']|serif|times|sans-serif|cursive|verdana|fantasy|monospace|normal|oblique|italic|small-caps|bolder|bold|lighter|[xx]{1,2}-small|smaller|small|medium|larger|large|[x]{1,2}-large|[1-9]00)"
            ),
            g = new RegExp(
              "((?:font(?:-family|-style-|-variant|-weight|-size)?)" +
                r.source +
                m.source +
                "(?:[,\\s\\/]+" +
                m.source +
                ")*)"
            ),
            v = new RegExp(
              "(?:" +
                o.source +
                "|" +
                c.source +
                "|none|top|center|bottom|left|center|right|scroll|fixed|repeat|repeat-x|repeat-y|no-repeat|" +
                d.source +
                ")"
            ),
            y = new RegExp(
              "((?:background(?:-color|-image|-repeat|-attachment|-position)?" +
                r.source +
                v.source +
                "(?:[\\s]+" +
                v.source +
                ")*))"
            ),
            b = new RegExp(
              "((?:text-transform)" +
                r.source +
                "(?:none|capitalize|uppercase|lowercase))"
            ),
            w = new RegExp(
              "(?:" +
                c.source +
                "|thick|medium|thinnone|dotted|dashed|solid|double|groove|ridge|inset|outset|" +
                d.source +
                ")"
            ),
            j = new RegExp(
              "((?:(?:top-|right-|bottom-|left-)border(?:-width)?|(?:border(?:-width|-color|-style)?))" +
                r.source +
                w.source +
                "(?:[\\s]+" +
                w.source +
                ")*)"
            ),
            k = new RegExp(
              "(?:" +
                o.source +
                "|inside|outside|disc|circle|square|decimal|lower-roman|upper-roman|lower-alpha|upper-alpha|none)"
            ),
            Q = new RegExp(
              "((?:list-style(?:-type|-image|-position)?)" +
                r.source +
                k.source +
                "(?:[\\s]+" +
                k.source +
                ")*)"
            ),
            x = new RegExp(
              l.source +
                "|" +
                s.source +
                "|" +
                j.source +
                "|" +
                u.source +
                "|" +
                h.source +
                "|" +
                g.source +
                "|" +
                p.source +
                "|" +
                f.source +
                "|" +
                y.source +
                "|" +
                b.source +
                "|" +
                Q.source,
              "ig"
            ),
            _ = function (e) {
              var t = {
                  apos: 39,
                  quot: 34,
                  amp: 38,
                  lt: 60,
                  gt: 62,
                  nbsp: 160,
                  iexcl: 161,
                  cent: 162,
                  pound: 163,
                  curren: 164,
                  yen: 165,
                  brvbar: 166,
                  sect: 167,
                  uml: 168,
                  copy: 169,
                  ordf: 170,
                  laquo: 171,
                  not: 172,
                  shy: 173,
                  reg: 174,
                  macr: 175,
                  deg: 176,
                  plusmn: 177,
                  sup2: 178,
                  sup3: 179,
                  acute: 180,
                  micro: 181,
                  para: 182,
                  middot: 183,
                  cedil: 184,
                  sup1: 185,
                  ordm: 186,
                  raquo: 187,
                  frac14: 188,
                  frac12: 189,
                  frac34: 190,
                  iquest: 191,
                  Agrave: 192,
                  Aacute: 193,
                  Acirc: 194,
                  Atilde: 195,
                  Auml: 196,
                  Aring: 197,
                  AElig: 198,
                  Ccedil: 199,
                  Egrave: 200,
                  Eacute: 201,
                  Ecirc: 202,
                  Euml: 203,
                  Igrave: 204,
                  Iacute: 205,
                  Icirc: 206,
                  Iuml: 207,
                  ETH: 208,
                  Ntilde: 209,
                  Ograve: 210,
                  Oacute: 211,
                  Ocirc: 212,
                  Otilde: 213,
                  Ouml: 214,
                  times: 215,
                  Oslash: 216,
                  Ugrave: 217,
                  Uacute: 218,
                  Ucirc: 219,
                  Uuml: 220,
                  Yacute: 221,
                  THORN: 222,
                  szlig: 223,
                  agrave: 224,
                  aacute: 225,
                  acirc: 226,
                  atilde: 227,
                  auml: 228,
                  aring: 229,
                  aelig: 230,
                  ccedil: 231,
                  egrave: 232,
                  eacute: 233,
                  ecirc: 234,
                  euml: 235,
                  igrave: 236,
                  iacute: 237,
                  icirc: 238,
                  iuml: 239,
                  eth: 240,
                  ntilde: 241,
                  ograve: 242,
                  oacute: 243,
                  ocirc: 244,
                  otilde: 245,
                  ouml: 246,
                  divide: 247,
                  oslash: 248,
                  ugrave: 249,
                  uacute: 250,
                  ucirc: 251,
                  uuml: 252,
                  yacute: 253,
                  thorn: 254,
                  yuml: 255,
                  OElig: 338,
                  oelig: 339,
                  Scaron: 352,
                  scaron: 353,
                  Yuml: 376,
                  fnof: 402,
                  circ: 710,
                  tilde: 732,
                  Alpha: 913,
                  Beta: 914,
                  Gamma: 915,
                  Delta: 916,
                  Epsilon: 917,
                  Zeta: 918,
                  Eta: 919,
                  Theta: 920,
                  Iota: 921,
                  Kappa: 922,
                  Lambda: 923,
                  Mu: 924,
                  Nu: 925,
                  Xi: 926,
                  Omicron: 927,
                  Pi: 928,
                  Rho: 929,
                  Sigma: 931,
                  Tau: 932,
                  Upsilon: 933,
                  Phi: 934,
                  Chi: 935,
                  Psi: 936,
                  Omega: 937,
                  alpha: 945,
                  beta: 946,
                  gamma: 947,
                  delta: 948,
                  epsilon: 949,
                  zeta: 950,
                  eta: 951,
                  theta: 952,
                  iota: 953,
                  kappa: 954,
                  lambda: 955,
                  mu: 956,
                  nu: 957,
                  xi: 958,
                  omicron: 959,
                  pi: 960,
                  rho: 961,
                  sigmaf: 962,
                  sigma: 963,
                  tau: 964,
                  upsilon: 965,
                  phi: 966,
                  chi: 967,
                  psi: 968,
                  omega: 969,
                  thetasym: 977,
                  upsih: 978,
                  piv: 982,
                  ensp: 8194,
                  emsp: 8195,
                  thinsp: 8201,
                  zwnj: 8204,
                  zwj: 8205,
                  lrm: 8206,
                  rlm: 8207,
                  ndash: 8211,
                  mdash: 8212,
                  lsquo: 8216,
                  rsquo: 8217,
                  sbquo: 8218,
                  ldquo: 8220,
                  rdquo: 8221,
                  bdquo: 8222,
                  dagger: 8224,
                  Dagger: 8225,
                  bull: 8226,
                  hellip: 8230,
                  permil: 8240,
                  prime: 8242,
                  Prime: 8243,
                  lsaquo: 8249,
                  rsaquo: 8250,
                  oline: 8254,
                  frasl: 8260,
                  euro: 8364,
                  image: 8465,
                  weierp: 8472,
                  real: 8476,
                  trade: 8482,
                  alefsym: 8501,
                  larr: 8592,
                  uarr: 8593,
                  rarr: 8594,
                  darr: 8595,
                  harr: 8596,
                  crarr: 8629,
                  lArr: 8656,
                  uArr: 8657,
                  rArr: 8658,
                  dArr: 8659,
                  hArr: 8660,
                  forall: 8704,
                  part: 8706,
                  exist: 8707,
                  empty: 8709,
                  nabla: 8711,
                  isin: 8712,
                  notin: 8713,
                  ni: 8715,
                  prod: 8719,
                  sum: 8721,
                  minus: 8722,
                  lowast: 8727,
                  radic: 8730,
                  prop: 8733,
                  infin: 8734,
                  ang: 8736,
                  and: 8743,
                  or: 8744,
                  cap: 8745,
                  cup: 8746,
                  int: 8747,
                  there4: 8756,
                  sim: 8764,
                  cong: 8773,
                  asymp: 8776,
                  ne: 8800,
                  equiv: 8801,
                  le: 8804,
                  ge: 8805,
                  sub: 8834,
                  sup: 8835,
                  nsub: 8836,
                  sube: 8838,
                  supe: 8839,
                  oplus: 8853,
                  otimes: 8855,
                  perp: 8869,
                  sdot: 8901,
                  lceil: 8968,
                  rceil: 8969,
                  lfloor: 8970,
                  rfloor: 8971,
                  lang: 9001,
                  rang: 9002,
                  loz: 9674,
                  spades: 9824,
                  clubs: 9827,
                  hearts: 9829,
                  diams: 9830,
                },
                a = {};
              for (var n in t) a[String.fromCharCode(t[n])] = n;
              return (e = e.replace(/url[(]([^)]+)[)]/g, function (e, a) {
                return (
                  "url('http://www.gmodules.com/ig/proxy?url=" +
                  (a = (a = a.replace(/^['"]|['"]$/g, "")).replace(
                    /&[a-z]{1,10};|&#x[a-f0-9]{1,3};|&#\d{1,3};|[^\w\/. -]/gi,
                    function (e) {
                      return e.length > 1
                        ? /^&#x/.test(e)
                          ? ((e = e.replace(/[&#x;]/g, "")),
                            "\\" + (e = parseInt(e, 16)).toString(16) + " ")
                          : /^&[a-z]/i.test(e)
                          ? "\\" +
                            (e = e.replace(/&(.+?);/g, function (e, a) {
                              return String.fromCharCode(
                                "#" != a[0]
                                  ? t[a]
                                  : "x" == a[1]
                                  ? parseInt(a.substr(2), 16)
                                  : parseInt(a.substr(1))
                              );
                            }))
                              .charCodeAt(0)
                              .toString(16) +
                            " "
                          : ((e = e.replace(/[&#;]/g, "")),
                            "\\" + (e = parseInt(e, 10)).toString(16) + " ")
                        : "\\" + e.charCodeAt(0).toString(16) + " ";
                    }
                  )) +
                  "')"
                );
              }));
            };
          return {
            parse: function (i) {
              var o = "";
              n = "";
              var r = !1;
              return (
                i.replace(x, function (a, i, l, s, c, d, u, p, f, h, m, g) {
                  if (void 0 !== c && c.length)
                    (n += "colour(" + c + ")\n"), (o += c + ";");
                  else if (void 0 !== i && i.length) {
                    (n += "selectorStart(" + i + ")\n"),
                      (r = !0),
                      (i = (i = i.replace(/{/, "")).split(","));
                    for (var v = 0; v < i.length; v++)
                      (i[v] = i[v].replace(/#(\w+)/, "#" + t + "_$1_")),
                        (i[v] = i[v].replace(/\.(\w+)/, "." + t + "_$1_")),
                        (i[v] = "#" + t + " " + i[v]);
                    o += i + " { \n";
                  } else if (void 0 !== l && l.length)
                    r &&
                      ((n += "selectorEnd(" + l + ")\n"),
                      (r = !1),
                      (o += "\n" + l + "\n"));
                  else if (void 0 !== d && d.length) {
                    if (e.disablePositioning) return "";
                    (n += "sizes(" + d + ")\n"), (o += d + ";");
                  } else if (void 0 !== u && u.length)
                    (n += "font(" + u + ")\n"),
                      (u = u.replace(/"/g, "'")),
                      (o += u + ";");
                  else if (void 0 !== m && m.length)
                    (n += "transform(" + m + ")\n"), (o += m + ";");
                  else if (void 0 !== p && p.length)
                    (n += "decoration(" + p + ")\n"), (o += p + ";");
                  else if (void 0 !== f && f.length) {
                    if (e.disablePositioning) return "";
                    (n += "alignment(" + f + ")\n"), (o += f + ";");
                  } else
                    void 0 !== s && s.length
                      ? ((n += "border(" + s + ")\n"), (o += s + ";"))
                      : void 0 !== h && h.length
                      ? ((n += "background(" + h + ")\n"),
                        (h = _(h)),
                        (o += h + ";"))
                      : void 0 !== g &&
                        g.length &&
                        ((n += "list(" + g + ")\n"),
                        (g = _(g)),
                        (o += g + ";"));
                }),
                a.rawOutput && a.rawOutput(o),
                a.parseTree && a.parseTree(n),
                o
              );
            },
            setAppID: function (e) {
              t = e;
            },
            setDebugObjs: function (e) {
              a = e;
            },
          };
        })()),
          (function (e) {
            var t = {
              init: function (a) {
                return this.each(function () {
                  var a = e(this);
                  if (!a.data("ngrid")) {
                    var n;
                    if (
                      !(n = e(this)
                        .attr("id")
                        .replace(/^grid-/, ""))
                    )
                      throw "ngrid: no id specified for grid";
                    e(this).data("ngrid", { init: !0, id: n, target: a }),
                      a.on(
                        "click.ngrid",
                        "a[href]:not([target])",
                        function (e) {
                          if (
                            "#" != this.href &&
                            !this.href.match(/^javascript:/)
                          )
                            return a.ngrid("reload", this.href), !1;
                        }
                      ),
                      a.on("submit.ngrid", "form:not([target])", function (n) {
                        return (
                          e(this).ajaxSubmit({
                            context: a,
                            cache: !1,
                            success: t.onAjaxSuccess,
                          }),
                          !1
                        );
                      }),
                      a.on("click.ngrid", ":button[data-url]", function (t) {
                        if (
                          e(this).attr("data-url") &&
                          !e(this).attr("data-target")
                        )
                          return (
                            a.ngrid("reload", e(this).attr("data-url")), !1
                          );
                      }),
                      a.on(
                        "change.ngrid",
                        "input.group-action-checkbox",
                        function () {
                          e(this)
                            .closest("tr")
                            .toggleClass("selected", this.checked);
                        }
                      ),
                      a.on(
                        "change.ngrid",
                        "input.group-action-checkbox-all",
                        function () {
                          var t = e("input.group-action-checkbox", a);
                          this.checked
                            ? t.prop("checked", !0)
                            : (e("input.group-action-checkbox").each(
                                function () {
                                  e(this).prop("disabled", !1);
                                }
                              ),
                              t.prop("checked", !1)),
                            t.trigger("change.ngrid"),
                            a.ngrid("info").totalRecords > t.length &&
                              (this.checked
                                ? a.find("div.am-check-all-offer").show()
                                : a.ngrid("toggleCheckAll", !1));
                        }
                      ),
                      a.on(
                        "click.ngrid",
                        "a.am-check-all-offer-offer",
                        function () {
                          a.ngrid("toggleCheckAll", !0),
                            e("input.group-action-checkbox").each(function () {
                              e(this).prop("checked", !0);
                            }),
                            e("input.group-action-checkbox").each(function () {
                              e(this).prop("disabled", !0);
                            });
                        }
                      ),
                      a.on(
                        "click.ngrid",
                        "a.am-check-all-offer-cancel",
                        function () {
                          e("input.group-action-checkbox").each(function () {
                            e(this).prop("disabled", !1);
                          }),
                            a.ngrid("toggleCheckAll", !1),
                            e("input.group-action-checkbox-all")
                              .prop("checked", !1)
                              .trigger("change.ngrid");
                        }
                      ),
                      a.on("click.ngrid", "td.expandable", t.onExpandableClick),
                      a.on(
                        "change.ngrid",
                        "div.am-group-wrap select",
                        function () {
                          if (this.selectedIndex) {
                            var t,
                              n,
                              i = "";
                            if (
                              ((t = e(
                                "input.group-action-checkbox-entire",
                                a
                              ).val())
                                ? (i = t)
                                : e("input.group-action-checkbox", a).each(
                                    function (e, t) {
                                      t.checked &&
                                        (i && (i += ","), (i += t.value));
                                    }
                                  ),
                              !i)
                            )
                              return (
                                flashError(
                                  "No rows selected for operation, please click on checkboxes, then repeat"
                                ),
                                (this.selectedIndex = null),
                                !1
                              );
                            n = e(this.options[this.selectedIndex]).attr(
                              "data-url"
                            );
                            var o = e(this.options[this.selectedIndex]).attr(
                              "data-target"
                            );
                            if (!n) throw "ngrid: no url specified for action";
                            i &&
                              (n +=
                                "&" +
                                escape("_" + a.data("ngrid").id + "_group_id") +
                                "=" +
                                escape(i)),
                              o ? (window.location = n) : a.ngrid("reload", n);
                          }
                        }
                      ),
                      e(window)
                        .resize(function () {
                          a.find(".am-grid").outerWidth() >
                          a.find(".am-grid-container").outerWidth()
                            ? a.find(".actions:last-child").each(function () {
                                e(this).parent().find(".checkboxes").length
                                  ? e(this)
                                      .parent()
                                      .find(".checkboxes")
                                      .after(e(this))
                                  : e(this).parent().prepend(e(this));
                              })
                            : a
                                .find(
                                  ".actions:first-child, .checkboxes ~ .actions"
                                )
                                .each(function () {
                                  e(this).parent().append(e(this));
                                });
                        })
                        .resize();
                  }
                  (n = window.location.hash.substr(1)) &&
                    e("td.expandable#" + n, a)
                      .not(".openedByHash")
                      .addClass("openedByHash")
                      .click(),
                    a.trigger("load"),
                    a.ngrid("reloadCached");
                });
              },
              reloadCached: function () {
                var t,
                  a = e(this)
                    .find(".need-reload")
                    .toArray()
                    .reduce(function (t, a) {
                      var n,
                        i = e(a).data("url");
                      return (
                        (t[i] = null !== (n = t[i]) && void 0 !== n ? n : []),
                        t[i].push(e(a)),
                        t
                      );
                    }, {});
                (window.reloadAjax =
                  null !== (t = window.reloadAjax) && void 0 !== t ? t : []),
                  window.reloadAjax.forEach(function (e) {
                    return e.abort();
                  }),
                  Object.keys(a).forEach(function (t) {
                    var n = e.ajax(t, { global: !1 });
                    n.then(function (e) {
                      a[t].forEach(function (t) {
                        var a;
                        return (
                          t.html(
                            null !== (a = e[t.data("key")]) && void 0 !== a
                              ? a
                              : e
                          ) && t.removeClass("need-reload")
                        );
                      });
                    }),
                      window.reloadAjax.push(n);
                  });
              },
              toggleCheckAll: function (t) {
                var a = e(this),
                  n = e("input.group-action-checkbox-all", a).parent(),
                  i = e("input.group-action-checkbox-entire", n);
                t
                  ? (i.val("[ALL]"),
                    e("div.am-check-all-offer-offer").hide(),
                    e("div.am-check-all-offer-selected").show())
                  : (i.val(""),
                    e("div.am-check-all-offer-offer").show(),
                    e("div.am-check-all-offer-selected").hide(),
                    e("div.am-check-all-offer").hide());
              },
              reload: function (a, n) {
                var i = e(this),
                  o = {
                    cache: !1,
                    context: i,
                    target: i,
                    url: a,
                    success: t.onAjaxSuccess,
                  };
                n && (o.data = n), e.ajax(o);
              },
              onAjaxSuccess: function (t, a, n, i) {
                var o = e(this);
                if ("object" == j(t) && t["ngrid-redirect"])
                  return o.ngrid("reload", t["ngrid-redirect"]);
                o.html(t),
                  e(document).scrollTop() > o.offset().top &&
                    e(document).scrollTop(o.offset().top),
                  e(window).resize();
              },
              onLoad: function (t) {
                e(this).on("load", t), t.apply(this);
              },
              info: function () {
                return JSON.parse(
                  e(this).find("table.am-grid").attr("data-info")
                );
              },
              onExpandableClick: function () {
                return (
                  (this.getText = function (t) {
                    return t.hasClass("isSafeHtml")
                      ? e(t).val()
                      : ((a = e(t).val()),
                        (w.disablePositioning = !0),
                        (w.validateHTML = !1),
                        w.parse(a));
                    var a;
                  }),
                  (this.close = function () {
                    this.row.data("state", "closed"),
                      this.row.removeClass("expanded"),
                      this.row.next().remove(),
                      this.row.find("td").removeClass("expanded");
                  }),
                  (this.open = function () {
                    if (
                      !this.cell.find(".data").hasClass("isAjax") ||
                      this.cell.data("loaded")
                    ) {
                      this.row.data("state", "opened"),
                        this.row.addClass("expanded"),
                        this.cell.data("openedByMe", 1);
                      var t = this.row.children().length;
                      this.row.after(
                        '<tr class="am-grid-row expandable-data-row"><td colspan="' +
                          t +
                          '" class="expandable-data break">' +
                          this.getText(this.cell.find(".data")) +
                          "</td></tr>"
                      ),
                        this.cell.addClass("expanded");
                    } else {
                      var a = this;
                      a.cell.data("loading") ||
                        (a.cell.data("loading", !0),
                        e.get(
                          this.cell.find(".data").val(),
                          null,
                          function (e) {
                            a.cell.data("loaded", !0),
                              a.cell.data("loading", !1),
                              a.cell.find(".data").val(e),
                              a.open();
                          }
                        ));
                    }
                  }),
                  (this.row = e(this).parent()),
                  (this.cell = e(this)),
                  (this.isHtml = this.cell.find(".data").hasClass("isHtml")),
                  (this.isSafeHtml = this.cell
                    .find(".data")
                    .hasClass("isSafeHtml")),
                  (this.state = this.row.data("state")),
                  (this.openedByMe = this.cell.data("openedByMe")),
                  this.row.children().data("openedByMe", 0),
                  "opened" == this.state
                    ? (this.close(), this.openedByMe || this.open())
                    : this.open(),
                  !1
                );
              },
            };
            e.fn.ngrid = function (a) {
              return t[a]
                ? t[a].apply(this, Array.prototype.slice.call(arguments, 1))
                : "object" !== j(a) && a
                ? void e.error(
                    "Method " + a + " does not exist on jQuery.ngrid"
                  )
                : t.init.apply(this, arguments);
            };
          })(jQuery);
      },
      60580: function (e, t, a) {
        "use strict";
        a.r(t);
        a(74916), a(23123), a(69826), a(41539), a(69600);
        !(function (e) {
          e.fn.onePerLine = function () {
            return this.each(function () {
              var n = e(this);
              if (!n.data("initialized")) {
                if ("textarea" != this.type)
                  throw new Error(
                    "Element should be textarea to use onePerLine for it"
                  );
                n.data("initialized", 1),
                  n.wrap("<div />"),
                  n.parent().sortable({
                    items: ".one-per-line-item",
                    update: function (e, a) {
                      t(n);
                    },
                  }),
                  n.hide();
                var i = n.val().length ? n.val().split("\n") : [],
                  o = e("<div />");
                o.append(
                  '<input type="text" style="width:80%"/> <a href="javascript:;" class="button">+</a>'
                ),
                  n.after(o),
                  o.find("input").keypress(function (t) {
                    if (13 == t.keyCode)
                      return (
                        e(this).closest("div").find("input").val() &&
                          (a(n, e(this).closest("div").find("input").val()),
                          e(this).closest("div").find("input").val("")),
                        !1
                      );
                  }),
                  o.find("a").click(function () {
                    e(this).closest("div").find("input").val() &&
                      (a(n, e(this).closest("div").find("input").val()),
                      e(this).closest("div").find("input").val(""));
                  });
                for (var r = 0; r < i.length; r++) a(n, i[r]);
              }
            });
            function t(t) {
              var a = [],
                n = 0;
              t
                .closest("div")
                .find(".one-per-line-item")
                .each(function (t, i) {
                  a[n++] = e(i).find(".one-per-line-item-content").text();
                }),
                t.val(a.join("\n"));
            }
            function a(a, n) {
              var i = e(
                  '<div style="padding-bottom:0.4em" class="one-per-line-item" />'
                ),
                o = e(
                  '<span class="one-per-line-item-content" style="cursor: move">'
                ).text(n);
              i.append(o),
                i
                  .prepend(" ")
                  .prepend(
                    e(
                      '<a href="javascript:;" style="text-decoration:none">&#10005;</a>'
                    )
                  ),
                a.before(i),
                i.find("a").click(function () {
                  e(this).closest("div").remove(), t(a);
                }),
                t(a);
            }
          };
        })(jQuery);
      },
      10073: function (e, t, a) {
        "use strict";
        a.r(t);
        a(69826),
          a(41539),
          a(47042),
          a(82526),
          a(41817),
          a(32165),
          a(66992),
          a(78783),
          a(33948);
        function n(e) {
          return (
            (n =
              "function" == typeof Symbol && "symbol" == typeof Symbol.iterator
                ? function (e) {
                    return typeof e;
                  }
                : function (e) {
                    return e &&
                      "function" == typeof Symbol &&
                      e.constructor === Symbol &&
                      e !== Symbol.prototype
                      ? "symbol"
                      : typeof e;
                  }),
            n(e)
          );
        }
        !(function (e) {
          var t = {
            init: function (t) {
              return this.each(function () {
                var a = e(this);
                if (!a.data("reupload")) {
                  var n = e.extend(
                    {
                      onChange: function () {},
                      onSuccess: function (e) {},
                      onSelect: function () {},
                      onSubmit: function () {},
                    },
                    t
                  );
                  a.data("params", n).data("reupload", 1),
                    a.amReUpload("drawUpload"),
                    a.data("params").onChange.call(a);
                }
              });
            },
            drawUpload: function () {
              var t = this;
              t.amReUpload("destroyUploader");
              var a = e('<div class="upload-control"></div>'),
                n = t.amReUpload("getUploader");
              t.before(a.append(n)), t.amReUpload("initUploader", n);
            },
            addFile: function (e) {
              if (!e.ok)
                return (
                  alert("Error: " + e.error), void this.amReUpload("drawUpload")
                );
              window.location.href = this.data("return-url");
            },
            destroyUploader: function () {
              var t = this,
                a = t.closest("div").find(".upload-control-upload");
              a.data("intervalId") && clearInterval(a.data("intervalId")),
                t.closest("div").find("div.upload-control").remove(),
                e("#uploader-iframe-" + t.attr("id")).remove(),
                e("#uploader-form-" + t.attr("id")).remove();
            },
            getUploader: function () {
              var t = e("<span></span>").text(am_i18n.upload_upload);
              return e(
                '<div class="upload-control-upload upload-control-reupload"></div>'
              )
                .css({
                  display: "inline-block",
                  overflow: "hidden",
                  float: "left",
                })
                .append(t);
            },
            initUploader: function (t) {
              var a = this,
                n = a.attr("id"),
                i = e('<input type="file" />').attr("name", "upload"),
                o = e("<form></form>")
                  .attr({
                    method: "post",
                    enctype: "multipart/form-data",
                    action: amUrl("/admin-upload/re-upload"),
                    target: "uploader-iframe-" + n,
                    id: "uploader-form-" + n,
                  })
                  .css({ margin: 0, padding: 0 }),
                r = e("<input />").attr({
                  name: "id",
                  value: a.data("upload_id"),
                  type: "hidden",
                });
              o.append(i).append(r);
              var l = e("<iframe></iframe>").attr({
                name: "uploader-iframe-" + n,
                id: "uploader-iframe-" + n,
              });
              e("body").append(o), e("body").append(l), l.hide();
              var s = i
                  .wrap("<div></div>")
                  .parent()
                  .css({
                    overflow: "hidden",
                    width: t.outerWidth(),
                    height: t.outerHeight(),
                  })
                  .css({ position: "absolute", "z-index": 1e4 }),
                c = setInterval(function () {
                  s.css("width") != t.outerWidth() &&
                    s.css("width", t.outerWidth()),
                    s.css("height") != t.outerHeight() &&
                      s.css("height", t.outerHeight());
                }, 250);
              t.data("intervalId", c),
                i.css({ float: "right" }),
                s.css({ opacity: 0 }),
                i.on("mouseover mouseout", function () {
                  t.toggleClass("hover");
                }),
                t.mousemove(function (e) {
                  s.css({
                    top: t.offset().top + "px",
                    left: t.offset().left + "px",
                  });
                }),
                i.change(function () {
                  a.data("params").onSelect.call(a),
                    a.data("params").onSubmit.call(a),
                    t
                      .find("span")
                      .empty()
                      .append(am_i18n.upload_uploading)
                      .addClass("uploading"),
                    o.submit(),
                    l.on("load", function () {
                      var t = document.getElementById(l.attr("id")),
                        n = e(t.contentWindow.document.body).text();
                      try {
                        n = JSON.parse(n);
                      } catch (e) {
                        n = {
                          ok: !1,
                          error: "Error of file uploading on server side",
                        };
                      }
                      setTimeout(function () {
                        a.amReUpload("addFile", n);
                      }, 10);
                    });
                });
            },
          };
          e.fn.amReUpload = function (a) {
            return t[a]
              ? t[a].apply(this, Array.prototype.slice.call(arguments, 1))
              : "object" !== n(a) && a
              ? void e.error("Method " + a + " does not exist on jQuery.upload")
              : t.init.apply(this, arguments);
          };
        })(jQuery);
      },
      62320: function (e, t, a) {
        "use strict";
        a.r(t);
        a(68309);
        jQuery.fn.serializeAssoc = function () {
          var e = {},
            t = jQuery(this).serializeArray();
          for (var a in t) e[t[a].name] = t[a].value;
          return e;
        };
      },
      8146: function (e, t, a) {
        "use strict";
        a.r(t);
        a(74916), a(15306), a(69826), a(41539);
        !(function (e) {
          e.fn.translate = function (t) {
            var a = [];
            return (
              this.each(function () {
                var n = this,
                  i = e(n);
                if (!e(n).data("initialized")) {
                  if ("text" != this.type && "textarea" != this.type)
                    throw new Error(
                      "Element should be text or textarea in order to use translator for it. [" +
                        this.type +
                        "] given."
                    );
                  e(n).data("initialized", 1);
                  e.extend({}, t);
                  var o = e('<div style="display:none;"></div>'),
                    r = e(
                      '<a class="local" href="javascript:;" style="display:none"></a>'
                    );
                  r.click(function () {
                    o.dialog("open");
                  }),
                    e(n).after(r),
                    r.before(" "),
                    e("body").append(o),
                    e(n)
                      .on("keyup change", function () {
                        r.toggle("" != e(this).val());
                      })
                      .keyup(),
                    i.on("change", function () {
                      l();
                    }),
                    a.push({
                      text: i.val().replace(/\r?\n/g, "\r\n"),
                      callback: function (e, t) {
                        l(e, t);
                      },
                    }),
                    o.dialog({
                      autoOpen: !1,
                      modal: !0,
                      title: "Translations",
                      width: 600,
                      position: { my: "center", at: "center", of: window },
                      buttons: {
                        Save: function () {
                          o.find("form").ajaxSubmit({
                            success: function () {
                              o.dialog("close"), l();
                            },
                          });
                        },
                        Cancel: function () {
                          e(this).dialog("close");
                        },
                      },
                    });
                }
                function l(e, t) {
                  0 == arguments.length ? s(i.val()) : (c(e), d(t));
                }
                function s(t) {
                  e.ajax({
                    type: "post",
                    data: { text: t.replace(/\r?\n/g, "\r\n") },
                    url: amUrl("/admin-trans-local/synchronize"),
                    dataType: "json",
                    success: function (e, t, a) {
                      c(e.stat), d(e.form);
                    },
                  });
                }
                function c(e) {
                  e.total &&
                    r
                      .empty()
                      .append(
                        "Translate (" + e.translated + "/" + e.total + ")"
                      );
                }
                function d(e) {
                  o.empty().append(e);
                }
              }),
              a.length &&
                (function (t) {
                  var a = [];
                  for (var n in t) a.push(t[n].text);
                  e.ajax({
                    type: "post",
                    data: { text: a },
                    url: amUrl("/admin-trans-local/synchronize-batch"),
                    dataType: "json",
                    success: function (e, a, n) {
                      for (var i in t)
                        t[i].callback(e[t[i].text].stat, e[t[i].text].form);
                    },
                  });
                })(a),
              this
            );
          };
        })(jQuery);
      },
      33673: function (e, t, a) {
        "use strict";
        a.r(t);
        a(74916),
          a(15306),
          a(23123),
          a(69826),
          a(41539),
          a(4723),
          a(39714),
          a(68309),
          a(40561),
          a(69600),
          a(92222),
          a(24603),
          a(47042),
          a(82526),
          a(41817),
          a(32165),
          a(66992),
          a(78783),
          a(33948);
        function n(e) {
          return (
            (n =
              "function" == typeof Symbol && "symbol" == typeof Symbol.iterator
                ? function (e) {
                    return typeof e;
                  }
                : function (e) {
                    return e &&
                      "function" == typeof Symbol &&
                      e.constructor === Symbol &&
                      e !== Symbol.prototype
                      ? "symbol"
                      : typeof e;
                  }),
            n(e)
          );
        }
        !(function (e) {
          var t = {
            init: function (t) {
              return this.each(function () {
                var a = e(this);
                if (!a.data("upload")) {
                  var n = e.extend(
                    {
                      onChange: function (e) {},
                      onFileAdd: function (e) {},
                      onFileDel: function (e) {},
                      onFileDraw: function (e) {},
                      onSelect: function () {},
                      onSubmit: function () {},
                      fileMime: !1,
                      fileMaxSize: !1,
                      fileMaxNum: !1,
                      fileBrowser: !0,
                      urlUpload: "/admin-upload/upload",
                      urlGet: "/admin-upload/get",
                    },
                    t
                  );
                  a.data("params", n).data("upload", 1);
                  var i,
                    o = a.attr("name"),
                    r = o.substr(o.length - 2, 2),
                    l = a.amUpload("info"),
                    s = a.amUpload("error"),
                    c = a.closest(".am-element");
                  for (
                    c.prepend(
                      e('<input type="hidden" value="-1">').attr("name", o)
                    ),
                      i = 0;
                    i < s.length;
                    i++
                  )
                    c.append(e('<span class="am-error"></span>').text(s[i]));
                  "[]" == r && a.data("multiple", 1),
                    a.data(
                      "validate-element",
                      "_" + a.attr("name").replace("[]", "")
                    ),
                    a.amUpload("drawUpload");
                  var d = e('<input type="text" />')
                    .attr("name", a.data("validate-element"))
                    .css({
                      opacity: 0,
                      width: 0,
                      height: 0,
                      padding: 0,
                      visibility: "hidden",
                    });
                  if (
                    (a.after(d),
                    setTimeout(function () {
                      var e;
                      if ((e = a.closest("form").data("validator")))
                        for (var t in e.settings.rules)
                          t == a.attr("name").replace("[]", "") &&
                            ((e.settings.rules[a.data("validate-element")] =
                              e.settings.rules[t]),
                            (e.settings.messages[a.data("validate-element")] =
                              e.settings.messages[t]));
                    }, 0),
                    a.attr("value"))
                  )
                    if (a.data("multiple")) {
                      var u = a.attr("value").split(",");
                      for (i = 0; i < u.length; i++)
                        (l[u[i]].upload_id = u[i]),
                          a.amUpload("drawFile", l[u[i]]);
                    } else
                      (l[a.attr("value")].upload_id = a.attr("value")),
                        e(this).amUpload("drawFile", l[a.attr("value")]);
                  a.hide(),
                    a.attr("disabled", "disabled"),
                    a.data("params").onChange.call(a, a.amUpload("count"));
                }
              });
            },
            increaseCount: function () {
              this.data("count", this.amUpload("count") + 1),
                1 == this.amUpload("count") &&
                  this.parent().find(".am-error").not("input").remove();
            },
            decreaseCount: function () {
              this.data("count", this.amUpload("count") - 1);
            },
            count: function () {
              return this.data("count") ? this.data("count") : 0;
            },
            drawFile: function (t) {
              var a = this;
              a.amUpload("destroyUploader");
              var n = e(
                '<a href="javascript:;" class="am-link-del">&#10005;</a>'
              );
              n.attr("aria-label", am_i18n.upload_remove_file);
              var i,
                o = e("<div></div>").data("info", t),
                r = a.data("params").urlGet;
              r
                ? ((r = amUrl(r)),
                  (i = e('<a class="link"></a>')),
                  (r += r.match(/\?/) ? "&" : "?"),
                  i
                    .attr(
                      "href",
                      r + "id=" + t.upload_id.toString().split("|", 2)[0]
                    )
                    .attr("target", "_top"))
                : (i = e("<span></span>")),
                a.before(
                  o
                    .append(i.append(t.name))
                    .append(" (" + t.size_readable + ")")
                    .append(" ")
                    .append(n)
                    .append(
                      e('<input type="hidden" />')
                        .attr("name", a.attr("name"))
                        .attr("value", t.upload_id)
                    )
                ),
                n.click(function () {
                  var t = e(this).closest("div").data("info");
                  e(this).closest("div").remove(),
                    a.amUpload("decreaseCount"),
                    a.amUpload("destroyUploader"),
                    a.amUpload("drawUpload"),
                    a
                      .data("value")
                      .splice(a.data("value").indexOf(t.upload_id), 1),
                    e("[name=" + a.data("validate-element") + "]")
                      .val(a.data("value").join(","))
                      .change(),
                    a.data("params").onChange.call(a, a.amUpload("count")),
                    a.data("params").onFileDel.call(a, t);
                }),
                a.data("value", (a.data("value") || []).concat([t.upload_id])),
                e("[name=" + a.data("validate-element") + "]")
                  .val(a.data("value").join(","))
                  .change(),
                a.data("params").onFileDraw.call(a, t),
                a.amUpload("increaseCount"),
                a.amUpload("drawUpload");
            },
            drawUpload: function () {
              var t = this;
              if (
                (t.amUpload("destroyUploader"),
                (t.data("multiple") || !t.amUpload("count")) &&
                  !(
                    t.data("params").fileMaxNum &&
                    t.amUpload("count") >= t.data("params").fileMaxNum
                  ))
              ) {
                var a = t.data("params").fileBrowser
                    ? e(
                        '<div class="upload-control-browse"><span>' +
                          am_i18n.upload_browse +
                          "</span></div>"
                      )
                    : "",
                  n = e('<div class="upload-control"></div>');
                t.amUpload("count") && n.css("margin-top", "1em");
                var i = t.amUpload("getUploader");
                t.before(n.append(i).append(a)),
                  t.data("params").fileBrowser && a.before(" ");
                var o = e("<div></div>");
                e("body").append(o),
                  o.hide(),
                  o.addClass("filesmanager-container"),
                  (o.get(0).uploader = t),
                  t.data("params").fileBrowser &&
                    (a.click(function () {
                      o.dialog({
                        modal: !0,
                        title: am_i18n.upload_files,
                        width: 800,
                        height: 600,
                        buttons: {
                          Cancel: function () {
                            e(this).dialog("close");
                          },
                        },
                        open: function () {
                          e.get(
                            amUrl("/admin-upload/grid"),
                            {
                              prefix: t.data("prefix"),
                              secure: t.data("secure"),
                            },
                            function (t, a, n) {
                              o.empty().append(t), e(".am-grid-wrap").ngrid();
                            }
                          );
                        },
                        close: function () {
                          o.empty(), o.remove();
                        },
                      });
                    }),
                    a.on("mouseover mouseout", function () {
                      a.toggleClass("hover");
                    })),
                  t.amUpload("initUploader", i);
              }
            },
            addFile: function (t) {
              var a = this;
              return t.ok
                ? (function (e) {
                    if (!a.data("params").fileMime) return !0;
                    var t = !1;
                    return (
                      jQuery.each(a.data("params").fileMime, function (a, n) {
                        (n = n
                          .replace(/[.+?^${}()|[\]\\]/g, "\\$&")
                          .replace(/\*/, ".+")),
                          e.match(new RegExp(n)) && (t = !0);
                      }),
                      t
                    );
                  })(t.mime)
                  ? (e(this).amUpload("drawFile", t),
                    a.data("params").onChange.call(a, a.amUpload("count")),
                    void a.data("params").onFileAdd.call(a, t))
                  : (alert(
                      "Incorrect file type : " +
                        t.mime +
                        ". Expect one of: " +
                        a.data("params").fileMime.join(", ")
                    ),
                    void a.amUpload("drawUpload"))
                : (alert("Error: " + t.error), void a.amUpload("drawUpload"));
            },
            myId: function () {
              return this.attr("id").replace(".", "-");
            },
            info: function () {
              return this.data("info");
            },
            error: function () {
              return this.data("error");
            },
            destroyUploader: function () {
              var t = this.closest("div").find(".upload-control-upload");
              t.data("intervalId") && clearInterval(t.data("intervalId")),
                this.closest("div").find("div.upload-control").remove(),
                e("#uploader-iframe-" + e(this).amUpload("myId")).remove(),
                e("#uploader-form-" + e(this).amUpload("myId")).remove();
            },
            getUploader: function () {
              var t = e("<span></span>").text(am_i18n.upload_upload),
                a = e('<div class="upload-control-upload"></div>')
                  .css({
                    display: "inline-block",
                    overflow: "hidden",
                    float: "left",
                  })
                  .append(t);
              return (
                !this.data("params").fileBrowser &&
                  a.addClass("upload-control-upload-single"),
                a
              );
            },
            initUploader: function (t) {
              var a = this,
                n = e(this).amUpload("myId"),
                i = e('<input type="file" />').attr("name", "upload");
              a.data("params").fileMime &&
                i.attr("accept", a.data("params").fileMime.join(","));
              var o = amUrl(a.data("params").urlUpload, 1),
                r = e("<form></form>")
                  .attr({
                    method: "post",
                    enctype: "multipart/form-data",
                    action: o[0],
                    target: "uploader-iframe-" + n,
                    id: "uploader-form-" + n,
                  })
                  .css({ margin: 0, padding: 0 });
              o[1] &&
                e.each(o[1], function (t, a) {
                  r.append(
                    e("<input />").attr({
                      name: a.name,
                      value: a.value,
                      type: "hidden",
                    })
                  );
                });
              var l = e("<input />").attr({
                  name: "prefix",
                  value: a.data("prefix"),
                  type: "hidden",
                }),
                s = e("<input />").attr({
                  name: "secure",
                  value: a.data("secure"),
                  type: "hidden",
                });
              if ((r.append(l).append(s), a.data("params").fileMaxSize)) {
                var c = e("<input />").attr({
                  name: "MAX_FILE_SIZE",
                  value: a.data("params").fileMaxSize,
                  type: "hidden",
                });
                r.append(c);
              }
              r.append(i);
              var d = e("<iframe></iframe>").attr({
                name: "uploader-iframe-" + n,
                id: "uploader-iframe-" + n,
              });
              e("body").append(r), e("body").append(d), d.hide();
              var u = i
                .wrap("<div></div>")
                .parent()
                .css({
                  overflow: "hidden",
                  width: t.outerWidth(),
                  height: t.outerHeight(),
                })
                .css({ position: "absolute", zIndex: 1e6 });
              setTimeout(function () {
                u.css({ width: t.outerWidth(), height: t.outerHeight() });
              }, 0);
              var p = setInterval(function () {
                u.css("width") != t.outerWidth() &&
                  u.css("width", t.outerWidth()),
                  u.css("height") != t.outerHeight() &&
                    u.css("height", t.outerHeight());
              }, 250);
              t.data("intervalId", p),
                i.css({ float: "right" }),
                u.css({ opacity: 0, display: "none" }),
                i.on("mouseover mouseout", function () {
                  t.toggleClass("hover");
                }),
                t.mousemove(function (e) {
                  u.css({ display: "block" }), u.offset(t.offset());
                }),
                i.change(function () {
                  a.data("params").onSelect.call(a),
                    a.data("params").onSubmit.call(a),
                    t
                      .find("span")
                      .empty()
                      .append(am_i18n.upload_uploading)
                      .addClass("uploading"),
                    d.on("load", function () {
                      var t = document.getElementById(d.attr("id")),
                        n = e(t.contentWindow.document.body).text();
                      try {
                        n = JSON.parse(n);
                      } catch (e) {
                        n = {
                          ok: !1,
                          error: "Error of file uploading on server side",
                        };
                      }
                      setTimeout(function () {
                        a.amUpload("addFile", n);
                      }, 0);
                    }),
                    r.submit();
                });
            },
          };
          e.fn.amUpload = function (a) {
            return t[a]
              ? t[a].apply(this, Array.prototype.slice.call(arguments, 1))
              : "object" !== n(a) && a
              ? void e.error("Method " + a + " does not exist on jQuery.upload")
              : t.init.apply(this, arguments);
          };
        })(jQuery);
      },
      38954: function (e, t, a) {
        "use strict";
        a.r(t);
        a(41539),
          a(54747),
          a(68309),
          a(74916),
          a(15306),
          a(82526),
          a(41817),
          a(32165),
          a(66992),
          a(78783),
          a(33948),
          a(91038),
          a(47042);
        var n = a(3100);
        function i(e) {
          return (
            (function (e) {
              if (Array.isArray(e)) return o(e);
            })(e) ||
            (function (e) {
              if (
                ("undefined" != typeof Symbol && null != e[Symbol.iterator]) ||
                null != e["@@iterator"]
              )
                return Array.from(e);
            })(e) ||
            (function (e, t) {
              if (!e) return;
              if ("string" == typeof e) return o(e, t);
              var a = Object.prototype.toString.call(e).slice(8, -1);
              "Object" === a && e.constructor && (a = e.constructor.name);
              if ("Map" === a || "Set" === a) return Array.from(e);
              if (
                "Arguments" === a ||
                /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a)
              )
                return o(e, t);
            })(e) ||
            (function () {
              throw new TypeError(
                "Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."
              );
            })()
          );
        }
        function o(e, t) {
          (null == t || t > e.length) && (t = e.length);
          for (var a = 0, n = new Array(t); a < t; a++) n[a] = e[a];
          return n;
        }
        var r = function (e) {
          if (!e.classList.contains("is-visible")) {
            var t = (function () {
              e.style.display = "block";
              var t = e.scrollHeight + "px";
              return (e.style.display = ""), t;
            })();
            e.classList.add("is-visible"),
              (e.style.height = t),
              window.setTimeout(function () {
                e.style.height = "";
              }, 350);
          }
        };
        (0, n.Z)(function () {
          i(
            window.document.body.getElementsByClassName("am-admin-help-div")
          ).forEach(function (e) {
            var t = JSON.parse(e.getAttribute("data-params")),
              a = document.createElement("iframe"),
              n = e.closest(".am-setup-form-wrapper");
            n && n.parentNode.appendChild(e);
            e.querySelector(".am-admin-help-readme") && r(e);
            var o = e.getAttribute("data-url");
            if (o) {
              (a.src = o),
                (a.style.display = "none"),
                (a.className = "am-admin-help-iframe"),
                e.appendChild(a),
                window.addEventListener("message", function (n) {
                  if ("height" == n.data.action && n.data.height > 0) {
                    var i = document.querySelector(".am-setup-form-wrapper"),
                      o = Math.max(
                        450,
                        Math.min(i.offsetHeight, n.data.height)
                      );
                    a.height = o + "px";
                  }
                  "inlineHelpLoaded" == n.data &&
                    (a.contentWindow.postMessage(
                      { action: "initInline", params: t },
                      "*"
                    ),
                    (a.style.display = "block"),
                    r(e));
                });
              var l = null,
                s = function (e) {
                  var t = e.target;
                  if ("INPUT" == t.tagName || "SELECT" == t.tagName) {
                    var n = t.closest(".am-row"),
                      o = {};
                    n &&
                      i(n.getElementsByClassName("admin-help")).forEach(
                        function (e) {
                          o.help_id = e.getAttribute("data-help_id");
                        }
                      ),
                      t.name &&
                        (o.name = t.name.replace(/^.+___([^_]+)$/, "$1")),
                      JSON.stringify(o) !== l &&
                        ((l = JSON.stringify(o)),
                        a.contentWindow.postMessage(
                          { action: "inlineScroll", params: o },
                          "*"
                        ));
                  }
                };
              window.addEventListener("focusin", s),
                window.addEventListener("click", s);
            }
          });
        });
      },
      78257: function (e, t, a) {
        "use strict";
        a.r(t);
        a(74916), a(15306), a(41539), a(54747), a(68309);
        (0, a(3100).Z)(function () {
          function e() {
            jQuery("#menu-editor, .menu-item")
              .sortable({
                items: "> .menu-item",
                handle: "> .menu-item-header",
                helper: "clone",
                connectWith: "#menu-editor, .menu-item",
                placeholder: "menu-item-placeholder",
                tolerance: "pointer",
                update: function (e, t) {
                  i();
                },
                stop: function (a, n) {
                  t(), e();
                },
              })
              .disableSelection();
          }
          function t() {
            jQuery(".ui-sortable").sortable("destroy");
          }
          function a(a, n, o, r) {
            var l =
              !(arguments.length > 4 && void 0 !== arguments[4]) ||
              arguments[4];
            (r = r || "#menu-editor"), t();
            var s = jQuery(
                '<a href="javascript:;" class="am-link-del" style="float:right">&#10005;</a>'
              ).click(function () {
                jQuery(this).closest(".menu-item").remove(), i();
              }),
              c = jQuery('<span class="menu-item-type" />').text(
                n.replace("-", " ")
              ),
              d = jQuery('<div class="menu-item-header" />')
                .text(a)
                .append(c)
                .append(s),
              u = jQuery('<div class="menu-item" />')
                .data("name", a)
                .data("id", n)
                .data("config", o)
                .append(d);
            return jQuery(r).append(u), l && i(), e(), u;
          }
          function n(e) {
            var t = [];
            return (
              jQuery("> .menu-item", e).each(function () {
                t.push({
                  name: jQuery(this).data("name"),
                  id: jQuery(this).data("id"),
                  config: jQuery(this).data("config"),
                  items: n(jQuery(this)),
                });
              }),
              t
            );
          }
          function i() {
            jQuery("#user_menu").val(JSON.stringify(n(jQuery("#menu-editor")))),
              o();
          }
          function o() {
            jQuery("#preview-user").data("user_id") &&
              jQuery("#menu-preview").load(amUrl("/admin-menu/preview"), {
                user_menu: jQuery("#user_menu").val(),
                user_id: jQuery("#preview-user").data("user_id"),
                menu_id: jQuery("#user_menu_id").val(),
              });
          }
          function r(e) {
            var t =
              arguments.length > 1 && void 0 !== arguments[1] && arguments[1];
            null == e ||
              e.forEach(function (e) {
                var n = a(e.name ? e.name : e.id, e.id, e.config || [], t, !1);
                r(e.items, n);
              });
          }
          document.getElementById("menu-editor-wrapper") &&
            (jQuery("#add-to-menu-directory").click(function () {
              jQuery("[name=directory_id]:checked").each(function () {
                a(
                  jQuery(this).data("title"),
                  jQuery(this).data("id"),
                  jQuery(this).data("config")
                ),
                  jQuery(this).prop("checked", !1),
                  jQuery("#available-items").dialog("close");
              });
            }),
            jQuery("#add-to-menu-container").click(function () {
              jQuery("#container-label").val()
                ? (a(jQuery("#container-label").val(), "container", {
                    label: jQuery("#container-label").val(),
                  }),
                  jQuery("#container-label").val(""),
                  jQuery("#available-items").dialog("close"))
                : alert("Please enter Container Label");
            }),
            jQuery("#add-to-menu-custom-link").click(function () {
              jQuery("#link-uri").val() && jQuery("#link-label").val()
                ? (a(jQuery("#link-label").val(), "custom-link", {
                    uri: jQuery("#link-uri").val(),
                    label: jQuery("#link-label").val(),
                    target: jQuery("#link-target").is(":checked")
                      ? "_blank"
                      : null,
                  }),
                  jQuery("#link-uri").val(""),
                  jQuery("#link-label").val(""),
                  jQuery("#link-target").prop("checked", !1),
                  jQuery("#available-items").dialog("close"))
                : alert("Please enter Link URL and Label");
            }),
            jQuery("#add-to-menu-form").click(function () {
              jQuery("[name=form_id]:checked").each(function () {
                a(
                  jQuery(this).data("title"),
                  jQuery(this).data("id"),
                  jQuery(this).data("config")
                ),
                  jQuery(this).prop("checked", !1),
                  jQuery("#available-items").dialog("close");
              });
            }),
            jQuery("#add-to-menu-link").click(function () {
              jQuery("[name=link_id]:checked").each(function () {
                a(
                  jQuery(this).data("title"),
                  jQuery(this).data("id"),
                  jQuery(this).data("config")
                ),
                  jQuery(this).prop("checked", !1),
                  jQuery("#available-items").dialog("close");
              });
            }),
            jQuery("#add-to-menu-page").click(function () {
              jQuery("[name=page_id]:checked").each(function () {
                a(
                  jQuery(this).data("title"),
                  jQuery(this).data("id"),
                  jQuery(this).data("config")
                ),
                  jQuery(this).prop("checked", !1),
                  jQuery("#available-items").dialog("close");
              });
            }),
            jQuery("#add-to-menu-folder").click(function () {
              jQuery("[name=folder_id]:checked").each(function () {
                a(jQuery(this).data("title"), "folder", {
                  id: jQuery(this).val(),
                }),
                  jQuery(this).prop("checked", !1),
                  jQuery("#available-items").dialog("close");
              });
            }),
            jQuery("#add-to-menu-special").click(function () {
              jQuery("[name=item_id]:checked").each(function () {
                a(jQuery(this).data("title"), jQuery(this).data("id"), {}),
                  jQuery(this).prop("checked", !1),
                  jQuery("#available-items").dialog("close");
              });
            }),
            jQuery("#menu-reset").click(function () {
              return confirm("Are you really want to reset menu?");
            }),
            jQuery("#menu-editor__add").click(function () {
              jQuery("#available-items").dialog({
                modal: !0,
                title: "Add Menu Item",
                width: 600,
                open: function () {
                  jQuery("#accordion").accordion({ autoHeight: !0 });
                },
              });
            }),
            jQuery(function () {
              r(JSON.parse(jQuery("#user_menu").val())), i();
            }),
            jQuery(function () {
              jQuery("#preview-user").on(
                "click",
                "#preview-user-text",
                function () {
                  jQuery(this).hide(),
                    jQuery("#preview-user-lookup").show().focus();
                }
              ),
                jQuery("#preview-user-lookup").autocomplete({
                  minLength: 2,
                  source: amUrl("/admin-users/autocomplete"),
                  select: function (e, t) {
                    return (
                      jQuery("#preview-user").data("user_id", t.item.user_id),
                      jQuery("#preview-user-lookup").val("").hide(),
                      jQuery("#preview-user-text").text(t.item.value),
                      jQuery("#preview-user-text").show(),
                      o(),
                      !1
                    );
                  },
                });
            }));
        });
      },
      24332: function (e, t, a) {
        "use strict";
        a.r(t);
        a(41539), a(54747), a(19601), a(74916), a(15306), a(69826);
        var n = a(38965),
          i =
            (a(47941),
            a(4723),
            a(47042),
            a(68309),
            a(91038),
            a(78783),
            a(82526),
            a(41817),
            a(32165),
            a(66992),
            a(33948),
            a(42531).Z.extend({
              hooks: {
                beforeRequest: [
                  function (e) {
                    e.headers.set("Accept", "application/json");
                  },
                ],
              },
            })),
          o = a(60190);
        function r(e, t) {
          var a =
            ("undefined" != typeof Symbol && e[Symbol.iterator]) ||
            e["@@iterator"];
          if (!a) {
            if (
              Array.isArray(e) ||
              (a = (function (e, t) {
                if (!e) return;
                if ("string" == typeof e) return l(e, t);
                var a = Object.prototype.toString.call(e).slice(8, -1);
                "Object" === a && e.constructor && (a = e.constructor.name);
                if ("Map" === a || "Set" === a) return Array.from(e);
                if (
                  "Arguments" === a ||
                  /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a)
                )
                  return l(e, t);
              })(e)) ||
              (t && e && "number" == typeof e.length)
            ) {
              a && (e = a);
              var n = 0,
                i = function () {};
              return {
                s: i,
                n: function () {
                  return n >= e.length
                    ? { done: !0 }
                    : { done: !1, value: e[n++] };
                },
                e: function (e) {
                  throw e;
                },
                f: i,
              };
            }
            throw new TypeError(
              "Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."
            );
          }
          var o,
            r = !0,
            s = !1;
          return {
            s: function () {
              a = a.call(e);
            },
            n: function () {
              var e = a.next();
              return (r = e.done), e;
            },
            e: function (e) {
              (s = !0), (o = e);
            },
            f: function () {
              try {
                r || null == a.return || a.return();
              } finally {
                if (s) throw o;
              }
            },
          };
        }
        function l(e, t) {
          (null == t || t > e.length) && (t = e.length);
          for (var a = 0, n = new Array(t); a < t; a++) n[a] = e[a];
          return n;
        }
        function s(e, t) {
          if (!(e instanceof t))
            throw new TypeError("Cannot call a class as a function");
        }
        function c(e, t) {
          for (var a = 0; a < t.length; a++) {
            var n = t[a];
            (n.enumerable = n.enumerable || !1),
              (n.configurable = !0),
              "value" in n && (n.writable = !0),
              Object.defineProperty(e, n.key, n);
          }
        }
        function d(e, t, a) {
          return (
            t in e
              ? Object.defineProperty(e, t, {
                  value: a,
                  enumerable: !0,
                  configurable: !0,
                  writable: !0,
                })
              : (e[t] = a),
            e
          );
        }
        var u = (function () {
          function e(t, a, n) {
            var i =
              arguments.length > 3 && void 0 !== arguments[3]
                ? arguments[3]
                : null;
            s(this, e),
              d(this, "_countrySelect", void 0),
              d(this, "_stateSelect", void 0),
              d(this, "_stateText", void 0),
              d(this, "_prevState", {}),
              d(this, "_prevCountry", void 0),
              d(this, "_selectStateText", (0, o.S)("select_state")),
              (this._countrySelect = t),
              (this._stateSelect = a),
              (this._stateText = n),
              (this._stateGetUrl = i),
              (this._selectStateText = (0, o.S)("select_state"));
          }
          var t, a, n;
          return (
            (t = e),
            (n = [
              {
                key: "setCache",
                value: function (t) {
                  e._cache = t;
                },
              },
              {
                key: "setDefaultUrl",
                value: function (t) {
                  e._defaultUrl = t;
                },
              },
              {
                key: "initDefaults",
                value: function (t) {
                  var a,
                    n = r(t.getElementsByTagName("select"));
                  try {
                    for (n.s(); !(a = n.n()).done; ) {
                      var i = a.value,
                        o = void 0,
                        l = void 0,
                        s = void 0;
                      if (
                        i.id &&
                        i.id.match(/^(f_country|f_cc_country)/) &&
                        ((o = i),
                        (l = t.querySelector(
                          "select#" + o.id.replace(/country/, "state")
                        )),
                        (s = t.querySelector(
                          "input#" + o.id.replace(/f(.+)country/, "t$1state")
                        )),
                        o && l && s)
                      ) {
                        if (o._hpCountrySelect) return;
                        (o._hpCountrySelect = new e(o, l, s, e._defaultUrl)),
                          o._hpCountrySelect.init();
                      }
                    }
                  } catch (e) {
                    n.e(e);
                  } finally {
                    n.f();
                  }
                },
              },
            ]),
            (a = [
              {
                key: "init",
                value: function () {
                  var e = this;
                  if (this._countrySelect.selectedIndex > 0) {
                    var t = this._countrySelect.value;
                    if (this._stateSelect.selectedIndex < 0) {
                      var a = this._stateText.value;
                      this._prevState[t] = a;
                    } else this._prevState[t] = this._stateSelect.value;
                  }
                  (this._countrySelect.onchange = function () {
                    e._countryChanged();
                  }),
                    this._countryChanged();
                },
              },
              {
                key: "_countryChanged",
                value: function () {
                  var t = this,
                    a = this._countrySelect.selectedIndex;
                  if (!(a < 0)) {
                    var n = this._countrySelect.options[a].value;
                    this._prevCountry &&
                      (this._prevState[this._prevCountry] =
                        this._getStateInput()),
                      void 0 === e._cache[n]
                        ? ((this._stateText.disabled = !0),
                          (this._stateSelect.disabled = !0),
                          i
                            .get(this._stateGetUrl.replace(/{COUNTRY}/, n))
                            .json()
                            .then(function (a) {
                              (e._cache[n] = a), t._setStates(n);
                            }))
                        : this._setStates(n),
                      (this._prevCountry = n);
                  }
                },
              },
              {
                key: "_getStateInput",
                value: function () {
                  return this._stateSelect.getAttribute("disabled")
                    ? this._stateText.value
                    : this._stateSelect.selectedIndex >= 0
                    ? this._stateSelect.options[this._stateSelect.selectedIndex]
                        .value
                    : void 0;
                },
              },
              {
                key: "_toggleText",
                value: function (e, t) {
                  (this._stateText.style.display = e ? "block" : "none"),
                    (this._stateText.disabled = !t),
                    this._stateText.setAttribute("_required", t);
                },
              },
              {
                key: "_toggleSelect",
                value: function (e, t) {
                  (this._stateSelect.style.display = e ? "block" : "none"),
                    (this._stateSelect.disabled = !t),
                    this._stateSelect.setAttribute("_required", t);
                },
              },
              {
                key: "_setStates",
                value: function (t) {
                  var a = e._cache[t];
                  if (a && Object.keys(a).length) {
                    this._toggleText(!1, !1),
                      this._toggleSelect(!0, !0),
                      (this._stateSelect.options.length = 0),
                      (this._stateSelect.selectedIndex = -1);
                    var n = document.createElement("option");
                    for (var i in ((n.innerText = this._selectStateText),
                    (n.value = ""),
                    this._stateSelect.add(n),
                    a))
                      (n = document.createElement("option")).setAttribute(
                        "value",
                        i
                      ),
                        (n.innerText = a[i]),
                        this._stateSelect.add(n),
                        this._prevState[t] == i &&
                          n.setAttribute("selected", "selected");
                  } else this._toggleSelect(!1, !1), this._toggleText(!0, !0);
                },
              },
            ]) && c(t.prototype, a),
            n && c(t, n),
            e
          );
        })();
        d(u, "_defaultUrl", void 0), d(u, "_cache", { "": null });
        a(88674);
        function p(e, t, a) {
          return (
            t in e
              ? Object.defineProperty(e, t, {
                  value: a,
                  enumerable: !0,
                  configurable: !0,
                  writable: !0,
                })
              : (e[t] = a),
            e
          );
        }
        function f(e) {
          var t = e.dialog_html,
            a = e.msg_ok,
            n = e.msg_cancel,
            i = e.dialog_title,
            o = document.createElement("div");
          "function" == typeof t ? t(o) : (o.innerHTML = t),
            document.body.appendChild(o);
          var r = function () {
            null != o &&
              (jQuery(o).dialog("destroy"),
              document.body.removeChild(o),
              (o = null));
          };
          return new Promise(function (e, t) {
            var l;
            jQuery(o).dialog({
              autoOpen: !0,
              width: 350,
              buttons:
                ((l = {}),
                p(l, a, function () {
                  o && jQuery(o).dialog("close");
                  var t = jQuery("form", o).serialize();
                  e(t), r();
                }),
                p(l, n, function () {
                  o && jQuery(o).dialog("close"), t(), r();
                }),
                l),
              closeOnEscape: !0,
              title: i,
              modal: !0,
            });
          });
        }
        function h(e) {
          var t = Object.assign({}, e);
          return function (e) {
            e.stopPropagation(), e.preventDefault();
            var a = this,
              n = jQuery(a).data("redirect");
            f(t)
              .then(function () {
                n && (window.location.href = url);
                var e = { type: "GET", url: a.href };
                return (
                  t.ajax_params && (e = t.ajax_params(e, a)), jQuery.ajax(e)
                );
              })
              .then(function (e) {
                if (e.ok) window.location.reload();
                else {
                  if (!e.redirect) throw e.msg;
                  window.location.href = e.redirect;
                }
              })
              .catch(function (e) {
                void 0 !== e &&
                  ((e = "string" == typeof e ? " : " + e : ""),
                  alert(t.msg_error + " " + e));
              });
          };
        }
        function m(e) {
          var t = jQuery(this);
          new Promise(function (e, t) {
            e();
          })
            .then(function () {
              return jQuery.ajax({ url: t.data("url") }).then(function (e) {
                return f({
                  dialog_html: e,
                  msg_ok: (0, o.S)("Send"),
                  msg_cancel: (0, o.S)("Cancel"),
                  msg_error: (0, o.S)("Unable to resend payment link"),
                  dialog_title: (0, o.S)("Re Send Payment Link"),
                });
              });
            })
            .then(function (e) {
              return jQuery.ajax({ type: "POST", url: t.data("url"), data: e });
            })
            .then(function (e) {
              flashMessage(e.msg);
            })
            .catch(function (e) {
              void 0 !== e &&
                flashError((0, o.S)("Unable to resend payment link"));
            });
        }
        function g(e) {
          var t = Object.assign({}, e);
          return function (e) {
            var a,
              n,
              i = jQuery(this);
            (t.dialog_html = function (e) {
              var o = "" + t.msg_text;
              (o += "<br/><br/>"),
                (o += t.msg_specify),
                (o +=
                  '<form><input class="datepicker" type="text" name="date" value="" id="rebill-date-picker" size="10"></form>'),
                (e.innerHTML = o),
                setTimeout(function () {
                  var o = jQuery(".datepicker", e);
                  o.datepicker({
                    dateFormat: window.uiDateFormat,
                    firstDay: window.uiWeekFirstDay || 0,
                    changeMonth: !0,
                    changeYear: !0,
                    onSelect: function (e) {
                      (a = o.datepicker("getDate")), (n = e);
                    },
                  }),
                    o.datepicker(
                      "setDate",
                      new Date(i.data(t.field).replace(/-/g, "/") + " 01:00:00")
                    ),
                    o.focus();
                }, 5);
            }),
              f(t)
                .then(function () {
                  var e =
                    a.getFullYear() +
                    "-" +
                    (1 + a.getMonth()) +
                    "-" +
                    a.getDate();
                  return jQuery.ajax({
                    type: "POST",
                    url: t.url + i.data("invoice_id"),
                    data: p({}, t.field, e),
                  });
                })
                .then(function (e) {
                  e.ok
                    ? (i.data(t.field, e.date), i.html(n))
                    : alert(t.msg_error + "\n" + e.msg);
                })
                .catch(function (e) {
                  "string" == typeof e && flashError(e);
                });
          };
        }
        function v(e, t, a) {
          return (
            t in e
              ? Object.defineProperty(e, t, {
                  value: a,
                  enumerable: !0,
                  configurable: !0,
                  writable: !0,
                })
              : (e[t] = a),
            e
          );
        }
        function y(e) {
          (e = !!e),
            jQuery(".user-invoice-pending").toggle(e),
            jQuery(".pending-invoices .show").toggle(!e),
            jQuery(".pending-invoices .hide").toggle(e);
        }
        function b() {
          jQuery(".grid-access-records tr").removeClass("odd"),
            jQuery(
              ".grid-access-records tr.am-grid-row.access:visible:odd"
            ).addClass("odd");
        }
        function w(e) {
          setTimeout(function () {
            1 == getCookie("am-show-expired") &&
              (jQuery(".user-access-title a:visible").click(),
              setCookie("am-show-expired", 1));
          }, 1);
          var t,
            a = e.user_id;
          e.convertDateList;
          e.aAccessEdit &&
            (function (e) {
              var t = e.accessEditUrl,
                a = e.convertDateList;
              function n(e) {
                var t;
                return null !== (t = a[e]) && void 0 !== t ? t : e;
              }
              jQuery(document).on("click", ".live-edit-date", function () {
                var e = jQuery(this);
                if ("edit" != e.data("mode")) {
                  e.data("mode", "edit");
                  var a = e.find("span.live-edit");
                  a.hide(),
                    e.find(".editable").hide(),
                    e.data("prev_value", n(a.text()));
                  var i = e.find("input.live-edit");
                  i.length ||
                    (i = jQuery(
                      '<input type="text" name="_access_begin_date" class="live-edit datepicker" size="10">'
                    )).appendTo(e),
                    initDatepicker(
                      i.show().val(n(e.find("span.live-edit").text())),
                      {
                        onClose: function (a, n) {
                          var i = jQuery(this).parent("td");
                          i.data("mode", "display"),
                            i
                              .find("span.live-edit")
                              .show()
                              .text(jQuery(this).val()),
                            i.find(".editable").show();
                          var o = jQuery(this).datepicker("getDate"),
                            r =
                              o.getFullYear() +
                              "-" +
                              (1 + o.getMonth()) +
                              "-" +
                              o.getDate();
                          if (a && a != i.data("prev_value")) {
                            var l = {
                              access_id: e.data("access_id"),
                              field: e.data("field"),
                              access_date: r,
                            };
                            jQuery.post(t, l, function (e) {
                              if (!e.success) return flashError(e.error), !1;
                              e.reload && window.location.reload();
                            });
                          }
                          jQuery(this).remove();
                        },
                      }
                    ).focus();
                }
              });
            })(e),
            (function (e) {
              var t = e.calculateAccessDatesUrl,
                a = e.user_id;
              jQuery(document).on(
                "change",
                "#user-access-info select[name=product_id]",
                function () {
                  var e = this.form;
                  if (jQuery(this).val()) {
                    var n = t;
                    jQuery.post(
                      n,
                      { user_id: a, product_id: jQuery(this).val() },
                      function (t, a, n) {
                        jQuery("input[name=begin_date]", e).datepicker(
                          "setDate",
                          new Date(
                            t.begin_date.replace(/-/g, "/") + " 01:00:00"
                          )
                        ),
                          jQuery("input[name=expire_date]", e).datepicker(
                            "setDate",
                            new Date(
                              t.expire_date.replace(/-/g, "/") + " 01:00:00"
                            )
                          );
                      }
                    );
                  }
                }
              );
            })(e),
            e.pendingInvoicesBlock &&
              jQuery(".pending-invoices .show,.pending-invoices .hide").click(
                function (e) {
                  e.preventDefault(), y(!!jQuery(this).hasClass("show"));
                }
              ),
            (t = e.invoiceDetailsUrl),
            jQuery(".expand-details")
              .click(function () {
                var e = jQuery(this)
                  .closest(".user-invoice-header")
                  .next(".user-invoice-record")
                  .is(":visible");
                jQuery(this).toggleClass("open", !e),
                  jQuery(this)
                    .closest(".user-invoice")
                    .toggleClass("user-invoice-open", !e),
                  jQuery(this).find(".open").toggle(e),
                  jQuery(this).find(".close").toggle(!e);
                var a = jQuery(this),
                  n = jQuery(this).closest(".user-invoice");
                return (
                  n.data("loaded")
                    ? jQuery(this)
                        .closest(".user-invoice-header")
                        .next(".user-invoice-record")
                        .slideToggle("fast")
                    : jQuery(".user-invoice-record", n).load(
                        t + n.data("invoice_id"),
                        null,
                        function () {
                          n.data("loaded", !0),
                            a
                              .closest(".user-invoice-header")
                              .next(".user-invoice-record")
                              .slideToggle("fast");
                        }
                      ),
                  !1
                );
              })
              .closest(".user-invoice-header")
              .next(".user-invoice-record")
              .hide();
          var n,
            i,
            r,
            l = e.changeRebillDateUrl,
            s = e.changeDueDateUrl;
          function c() {
            jQuery(".add-payment-payment form").ajaxForm({
              target: ".add-payment-payment",
              beforeSubmit: function (e, t, a) {
                return (
                  jQuery("input[type='submit']", jQuery(t))
                    .val((0, o.S)("Submitting..."))
                    .attr("disabled", "disabled"),
                  !0
                );
              },
              success: function () {
                c(),
                  jQuery(".add-payment-payment input[name='saved-ok']")
                    .length && window.location.reload();
              },
            });
          }
          jQuery(document).on(
            "click",
            ".change-rebill-date",
            g({
              msg_ok: (0, o.S)("OK"),
              msg_cancel: (0, o.S)("Cancel"),
              msg_error: (0, o.S)("Unable to change rebill date"),
              msg_text:
                (0, o.S)(
                  "Do you really want to change Rebill Date for this invoice?"
                ) +
                (0, o.S)("User's access period will be updated accordingly"),
              msg_specify: (0, o.S)("Please specify new rebill date: "),
              dialog_title: (0, o.S)("Change Rebill Date"),
              field: "rebill_date",
              url: l,
            })
          ),
            jQuery(document).on(
              "click",
              ".change-due-date",
              g({
                msg_ok: (0, o.S)("OK"),
                msg_cancel: (0, o.S)("Cancel"),
                msg_error: (0, o.S)("Unable to change due date"),
                msg_text: (0, o.S)(
                  "Do you really want to change Due Date for this invoice?"
                ),
                msg_specify: (0, o.S)("Please specify new due date: "),
                dialog_title: (0, o.S)("Change Due Date"),
                field: "due_date",
                url: s,
              })
            ),
            jQuery(document).on(
              "click",
              ".stop-recurring",
              h({
                dialog_html: (0, o.S)(
                  "Do you really want to stop this subscription?"
                ),
                msg_ok: (0, o.S)("OK"),
                msg_cancel: (0, o.S)("Cancel"),
                msg_error: (0, o.S)("Unable to cancel subscription"),
                dialog_title: (0, o.S)("Cancel Subscription"),
              })
            ),
            jQuery(document).on(
              "click",
              ".start-recurring",
              h({
                dialog_html: (0, o.S)(
                  "Do you really want to resume this subscription?"
                ),
                msg_ok: (0, o.S)("OK"),
                msg_cancel: (0, o.S)("Cancel"),
                msg_error: (0, o.S)("Unable to resume subscription"),
                dialog_title: (0, o.S)("Resume Subscription"),
              })
            ),
            jQuery(document).on(
              "click",
              ".resend-payment-receipt",
              h({
                dialog_html: (0, o.S)(
                  "Do you want to resend Payment Receipt email for this payment?"
                ),
                msg_ok: (0, o.S)("Send"),
                msg_cancel: (0, o.S)("Cancel"),
                msg_error: (0, o.S)("Unable to resend receipt"),
                dialog_title: (0, o.S)("Resend Payment Receipt"),
              })
            ),
            jQuery(document).on("click", ".resend-payment-link", m),
            jQuery(document).on("change", "#refund-type", function () {
              jQuery("[rel=refund-amount]").toggle(
                "correction" !== jQuery(this).val()
              );
            }),
            jQuery(document).on("change", "#refund-manual", function () {
              jQuery(".refund-payment .refund-manual-options").toggle(
                jQuery(".refund-payment input#refund-manual").is(":checked")
              );
            }),
            (i = (n = e).refundAjaxUrl),
            (r = n.amDateNow),
            jQuery(document).on("click", ".refund-button", function () {
              var e,
                t = jQuery(this)
                  .attr("id")
                  .replace(/refund-button-/, ""),
                a = jQuery(this).hasClass("refund-button-manual"),
                n = a || "1" == jQuery(this).data("partialrefunds");
              jQuery(".refund-payment .am-errors").hide(),
                jQuery("[rel=refund-amount]").toggle(
                  !a || "correction" != jQuery("#refund-type").val()
                ),
                jQuery(".refund-payment-id").text(t),
                jQuery(".refund-payment .manual").toggle(a),
                jQuery(".refund-payment .auto").toggle(!a),
                jQuery(".refund-payment input#refund-amount").data(
                  "refund_amount",
                  jQuery(this).data("refund_amount")
                ),
                jQuery(".refund-payment input#refund-amount").val(
                  jQuery(this).data("refund_amount")
                ),
                jQuery(".refund-payment input#refund-dattm").val(r),
                jQuery(".refund-payment .refund-manual-options").toggle(
                  a ||
                    jQuery(".refund-payment input#refund-manual").is(":checked")
                ),
                jQuery(".refund-payment input#refund-amount").prop(
                  "disabled",
                  !n
                ),
                jQuery(".refund-payment input[name=manual]").change(
                  function () {
                    this.checked
                      ? jQuery(".refund-payment input#refund-amount").prop(
                          "disabled",
                          !1
                        )
                      : (jQuery(".refund-payment input#refund-amount").prop(
                          "disabled",
                          !n
                        ),
                        n ||
                          jQuery(".refund-payment input#refund-amount").val(
                            jQuery(".refund-payment input#refund-amount").data(
                              "refund_amount"
                            )
                          ));
                  }
                );
              var l = (0, o.S)("OK"),
                s = (0, o.S)("Cancel");
              jQuery(".refund-payment").dialog({
                autoOpen: !0,
                width: 350,
                buttons:
                  ((e = {}),
                  v(e, l, function (e) {
                    jQuery(".refund-payment .am-errors").hide();
                    var n = jQuery(e.target).closest(".ui-dialog-buttonset");
                    n
                      .find("button")
                      .attr("disabled", !0)
                      .addClass("ui-state-disabled")
                      .removeClass("ui-state-hover"),
                      jQuery.ajax({
                        type: "POST",
                        url: i + t,
                        data: {
                          dattm: jQuery(
                            ".refund-payment input#refund-dattm"
                          ).val(),
                          amount: jQuery(
                            ".refund-payment input#refund-amount"
                          ).val(),
                          type: jQuery(
                            ".refund-payment select#refund-type:visible"
                          ).val(),
                          manual:
                            0 +
                            (a ||
                              jQuery(".refund-payment input#refund-manual").is(
                                ":checked"
                              )),
                        },
                        success: function (e, t, a) {
                          n
                            .find("button")
                            .attr("disabled", !1)
                            .removeClass("ui-state-disabled"),
                            e.success
                              ? "redirect" === e.success
                                ? (window.location.href = e.url)
                                : window.location.reload()
                              : (jQuery(".refund-payment .am-errors ul li")
                                  .empty()
                                  .append(e.text),
                                jQuery(".refund-payment .am-errors").show());
                        },
                      });
                  }),
                  v(e, s, function () {
                    jQuery(this).dialog("close");
                  }),
                  e),
                closeOnEscape: !0,
                title: (0, o.S)("Refund Payment"),
                modal: !0,
              });
            }),
            jQuery(document).on("click", ".replace-product", function () {
              var e = jQuery(this).data("invoice_item_id");
              jQuery("#replace-product")
                .html((0, o.S)("Loading..."))
                .load(
                  amUrl("/admin-user-payments/replace-product?id=") +
                    e +
                    "&user_id=" +
                    a
                ),
                jQuery("#replace-product").dialog({
                  autoOpen: !0,
                  width: 600,
                  buttons: {},
                  closeOnEscape: !0,
                  title: (0, o.S)("Replace Product"),
                  modal: !0,
                });
            }),
            jQuery(document).on("submit", "#replace-product-form", function () {
              return (
                jQuery(this).ajaxSubmit({
                  success: function (e) {
                    e.ok
                      ? window.location.reload()
                      : jQuery("#replace-product").html(e);
                  },
                }),
                !1
              );
            }),
            jQuery(document).on("click", ".add-payment-link", function () {
              var e = jQuery(this);
              c(),
                jQuery(".add-payment-payment input[name=invoice_id]").val(
                  e.data("invoice_id")
                ),
                jQuery(".add-payment-payment select[name=paysys_id]").val(
                  e.data("paysys_id")
                );
              var t = jQuery(".add-payment-payment select[name=amount]");
              t.find("option").remove(),
                jQuery.each(e.data("amounts"), function (e, a) {
                  var n = t.prop("options");
                  n[n.length] = new Option(a, a, !0, !0);
                }),
                jQuery(".add-payment-payment").dialog({
                  autoOpen: !0,
                  width: 600,
                  closeOnEscape: !0,
                  title: (0, o.S)("Add Payment"),
                  modal: !0,
                });
            }),
            jQuery(document).on(
              "submit",
              "#user-access-info form",
              function () {
                var e = jQuery(this);
                if (!jQuery("input[name='begin_date']", e).val())
                  return flashError((0, o.S)("Please enter start date")), !1;
                if (!jQuery("input[name='expire_date']", e).val())
                  return flashError((0, o.S)("Please enter expire date")), !1;
                if (
                  jQuery("input[name='expire_date']", e).datepicker("getDate") <
                  jQuery("input[name='begin_date']", e).datepicker("getDate")
                )
                  return (
                    flashError(
                      (0, o.S)("Begin date should be before Expire date")
                    ),
                    !1
                  );
                if (!jQuery("select[name='product_id']", e).val())
                  return flashError((0, o.S)("Please choose product")), !1;
                var t = jQuery(":submit", e);
                return (
                  t.attr("disabled", "disabled").val(t.val() + "..."),
                  jQuery("#user-access-info").load(
                    e.attr("action") + " #user-access-info",
                    e.serializeArray(),
                    function () {
                      initDatepicker("input.datepicker"),
                        jQuery("select[name=product_id]").val(""),
                        jQuery("input[name=comment]").val(""),
                        b();
                    }
                  ),
                  !1
                );
              }
            ),
            jQuery(document).on("click", ".user-access-title a", function () {
              jQuery("#user-access-info-w").toggleClass(
                "display-all display-active"
              ),
                setCookie(
                  "am-show-expired",
                  0 == getCookie("am-show-expired") ||
                    null == getCookie("am-show-expired")
                    ? 1
                    : 0
                ),
                b();
            });
          var d,
            u = e.accessDeleteUrl;
          jQuery(document).on(
            "click",
            "#user-access-info a.access-delete",
            function () {
              var e = jQuery(this)
                .attr("id")
                .replace(/^access-delete-/, "");
              confirm("Really delete?") &&
                jQuery("#user-access-info").load(
                  u + " #user-access-info",
                  { id: e },
                  function () {
                    initDatepicker("#user-access-info input.datepicker"), b();
                  }
                );
            }
          ),
            jQuery(document).on("click", "a.invoice-link", function () {
              var e = this.href.replace(/.*#/, "");
              jQuery("a[name='" + e + "']")
                .next(".user-invoice-header")
                .find(".expand-details")
                .click();
            }),
            jQuery(".user-invoice-pending").on(
              "mouseenter mouseleave",
              function () {
                jQuery(this).toggleClass("user-invoice-pending");
              }
            ),
            (d = window.location.hash.substr(1)),
            jQuery("a[name='" + d + "']")
              .closest(".user-invoice")
              .hasClass("user-invoice-pending") && y(!0),
            jQuery("a[name='" + d + "']")
              .next(".user-invoice-header")
              .find(".expand-details")
              .click(),
            b();
        }
        function j() {
          document.querySelectorAll(".html-edit-hint").forEach(function (e) {
            (e.innerText = e
              .closest(".am-row")
              .querySelector("textarea")
              .value.substr(0, 50)),
              (e.innerHTML += "&hellip;");
          });
        }
        function k() {
          jQuery("select.magicselect").magicSelect(),
            jQuery("select.magicselect-sortable").magicSelect({ sortable: !0 }),
            setTimeout(function () {
              var e;
              jQuery("select.am-combobox")
                .select2(
                  (e = { minimumResultsForSearch: 10, width: "resolve" })
                )
                .data("select2-option", e),
                jQuery("select.am-combobox-fixed")
                  .select2(
                    (e = { minimumResultsForSearch: 10, width: "300px" })
                  )
                  .data("select2-option", e),
                jQuery("select.am-combobox-fixed-compact")
                  .select2(
                    (e = { minimumResultsForSearch: 10, width: "180px" })
                  )
                  .data("select2-option", e);
            }, 0),
            (0, n.amVar)("langCount") > 1 && jQuery(".translate").translate(),
            jQuery("input.options-editor").optionsEditor(),
            jQuery(".am-upload").amUpload(),
            jQuery(".am-reupload").amReUpload(),
            jQuery("input[type=file].styled").fileStyle(),
            jQuery(".one-per-line").onePerLine(),
            initDatepicker(),
            jQuery(".am-grid-wrap").ngrid(),
            j();
        }
        (window.amCountryState = u),
          amCountryState.setDefaultUrl(
            (0, n.amVar)("api-url") + "ajax?do=get_states&country={COUNTRY}"
          ),
          jQuery(function () {
            var e, t, a, i;
            (0, n.amVar)("datepickerDefaults") &&
              jQuery.datepicker.setDefaults((0, n.amVar)("datepickerDefaults")),
              document
                .querySelectorAll(
                  "#lang-switch-icon-open, #lang-switch-icon-close"
                )
                .forEach(function (e) {
                  e.addEventListener("click", function (e) {
                    var t = document.querySelector(".am-lang-choice"),
                      a = document.querySelector(".user-identity-block");
                    t &&
                      a &&
                      (Object.assign(t.style, {
                        top: "".concat(a.offsetTop, "px"),
                        left: "".concat(
                          a.offsetLeft + a.clientWidth - 200,
                          "px"
                        ),
                        width: "205px",
                        zIndex: 5,
                      }),
                      (t.style.display =
                        "block" == t.style.display ? "none" : "block"),
                      a.classList.toggle("user-identity-block_lang-open"));
                  });
                }),
              null ===
                (e = document.querySelector(".am-lang-choice [name=__lang]")) ||
                void 0 === e ||
                e.addEventListener("change", function (e) {
                  return e.target.form.submit();
                }),
              window.addEventListener("resize", function (e) {
                var t = document.querySelector(".am-lang-choice"),
                  a = document.querySelector(".user-identity-block");
                t &&
                  a &&
                  Object.assign(t.style, {
                    top: "".concat(a.offsetTop, "px"),
                    left: "".concat(a.offsetLeft + a.clientWidth - 200, "px"),
                  });
              });
            var r = document.getElementById("menu-wrapper");
            r &&
              ["mouseenter", "mouseleave"].forEach(function (e) {
                r.addEventListener(
                  e,
                  function (e) {
                    if (e.target.classList.contains("menu-top-level")) {
                      var t = e.target,
                        a = t.querySelector("ul");
                      return (
                        (a.style.display =
                          "block" == a.style.display ? "none" : "block"),
                        t.classList.toggle("active"),
                        !1
                      );
                    }
                  },
                  !0
                );
              }),
              null === (t = document.getElementById("menu-collapse-expand")) ||
                void 0 === t ||
                t.addEventListener("click", function (e) {
                  return (
                    document
                      .querySelector("body")
                      .classList.toggle("collapsed"),
                    "1" === getCookie("am-admin-menu-collapsed")
                      ? setCookie("am-admin-menu-collapsed", "0")
                      : setCookie("am-admin-menu-collapsed", "1"),
                    window.dispatchEvent(new Event("resize")),
                    !1
                  );
                }),
              null === (a = document.getElementById("move-to-top")) ||
                void 0 === a ||
                a.addEventListener("click", function (e) {
                  e.preventDefault(),
                    window.scrollTo({ top: 0, behavior: "smooth" });
                }),
              document
                .querySelectorAll(".am-tabs .has-children > ul")
                .forEach(function (e) {
                  ["mouseenter", "mouseleave"].forEach(function (t) {
                    e.addEventListener(t, function (e) {
                      var t = e.target.closest("li").classList;
                      t.toggle("active"), t.toggle("expanded");
                    });
                  });
                }),
              jQuery("input#user-lookup").autocomplete({
                minLength: 2,
                source: amUrl("/admin-users/autocomplete"),
                select: function (e, t) {
                  return (window.location = t.item.url), !1;
                },
              }),
              jQuery(document).on(
                "click",
                "._collapsible_ ._head_",
                function () {
                  jQuery(this).closest("._item_").toggleClass("_open_");
                }
              ),
              jQuery(document).on("click", ".am-secret-text-link", function () {
                var e = jQuery(this).closest(".am-secret-text");
                jQuery("span, a", e).hide(),
                  jQuery("input", e).prop("disabled", !1).show().focus();
              }),
              jQuery("#admin-login").submit(function () {
                return (
                  jQuery.ajax({
                    global: !1,
                    type: "POST",
                    url: jQuery("#admin-login form").attr("action"),
                    data: jQuery("#admin-login form").serializeArray(),
                    complete: function (e) {
                      var t = JSON.parse(e.responseText);
                      if (t)
                        if (t.ok) jQuery("#admin-login").dialog("destroy");
                        else if (-8 == t.code)
                          jQuery("#admin-login").empty().append(e.html);
                        else {
                          t.error || (t.error = ["Login failed"]);
                          var a = jQuery("#admin-login form"),
                            n = jQuery("#admin-login ul.am-errors");
                          n.length
                            ? n.empty()
                            : a.before(
                                (n = jQuery("<ul class='am-errors'></ul>"))
                              );
                          for (var i = 0; i < t.error.length; i++)
                            n.append("<li>" + t.error[i] + "</li>");
                          n.fadeTo("slow", 0.1).fadeTo("slow", 1),
                            t.recaptcha_key
                              ? (jQuery("#recaptcha-row").show(),
                                "undefined" == typeof grecaptcha
                                  ? ((window.onLoadGrecaptcha = function () {
                                      a.data(
                                        "recaptcha",
                                        grecaptcha.render("recaptcha-element", {
                                          sitekey: t.recaptcha_key,
                                          theme:
                                            jQuery("#recaptcha-row").data(
                                              "recaptcha-theme"
                                            ),
                                          size: jQuery("#recaptcha-row").data(
                                            "recaptcha-size"
                                          ),
                                        })
                                      );
                                    }),
                                    jQuery.getScript(
                                      "//www.google.com/recaptcha/api.js?onload=onLoadGrecaptcha&render=explicit"
                                    ))
                                  : void 0 === a.data("recaptcha")
                                  ? a.data(
                                      "recaptcha",
                                      grecaptcha.render("recaptcha-element", {
                                        sitekey: t.recaptcha_key,
                                        theme:
                                          jQuery("#recaptcha-row").data(
                                            "recaptcha-theme"
                                          ),
                                        size: jQuery("#recaptcha-row").data(
                                          "recaptcha-size"
                                        ),
                                      })
                                    )
                                  : grecaptcha.reset(a.data("recaptcha")))
                              : jQuery("#recaptcha-row").hide();
                        }
                      else window.location.href = amUrl("/admin");
                    },
                  }),
                  jQuery('#admin-login input[name="passwd"]').val(""),
                  !1
                );
              }),
              jQuery(document).ajaxComplete(function (e, t, a) {
                if (402 == t.status) {
                  var n = JSON.parse(t.responseText);
                  jQuery("#admin-login .am-error").text(n.err ? n.err : null),
                    jQuery("#admin-login").dialog({
                      modal: !0,
                      title: "Administrator Login",
                      width: "500",
                    });
                }
              }),
              jQuery(document).ajaxStart(function () {
                var e = jQuery("div.ajax-loading");
                e.data("ajaxActive", !0),
                  setTimeout(function () {
                    e.data("ajaxActive") && e.show();
                  }, 200);
              }),
              jQuery(document).ajaxStop(function () {
                jQuery("div.ajax-loading").data("ajaxActive", !1).hide();
              }),
              j(),
              jQuery(document).on("click", "a.html-edit", function () {
                var e = jQuery(this).data("element-id"),
                  t = jQuery(this).data("mce-options");
                jQuery("#" + jQuery(this).data("wrap-id")).dialog({
                  autoOpen: !0,
                  modal: !0,
                  title: jQuery(this).data("title"),
                  width: 800,
                  position: { my: "center", at: "center", of: window },
                  buttons: {
                    Ok: function () {
                      jQuery(this).dialog("close");
                    },
                  },
                  beforeClose: function (t, a) {
                    destroyCkeditor(e);
                  },
                  close: function (e, t) {
                    jQuery(this).dialog("destroy"), j();
                  },
                  create: function (a, n) {
                    initCkeditor(e, t);
                  },
                });
              }),
              jQuery(document).on("click", "a.email-template", function () {
                if (jQuery(this).data("loading")) return !1;
                jQuery(this).data("loading", !0);
                var e = jQuery(
                  '<div style="display:none;" id="email-template-popup"></div>'
                );
                jQuery("body").append(e);
                var t = jQuery(this).data("href"),
                  a = t.replace(/\?.*$/, ""),
                  n = t.replace(/^.*?\?/, ""),
                  i = jQuery(this);
                return (
                  e.dialog({
                    autoOpen: !1,
                    modal: !0,
                    title: "Email Template",
                    width: 800,
                    buttons: {
                      Save: function () {
                        e.find("form#EmailTemplate").ajaxSubmit({
                          success: function (t) {
                            if (t.content)
                              i.closest(".am-element")
                                .empty()
                                .append(t.content);
                            else if (t)
                              return void jQuery("#email-template-popup").html(
                                t
                              );
                            e.dialog("close");
                          },
                          beforeSerialize: function () {
                            if (CKEDITOR && "undefined" != CKEDITOR)
                              for (var e in CKEDITOR.instances)
                                CKEDITOR.instances[e].updateElement();
                          },
                        });
                      },
                      Cancel: function () {
                        jQuery(this).dialog("close");
                      },
                    },
                    close: function () {
                      e.remove();
                    },
                  }),
                  jQuery.ajax({
                    type: "get",
                    data: n,
                    url: a,
                    dataType: "html",
                    success: function (t, a, n) {
                      e.empty().append(t), e.dialog("open");
                    },
                    complete: function (e, t) {
                      i.data("loading", !1);
                    },
                  }),
                  !1
                );
              }),
              jQuery(document).on("click", ".ajax-link", function () {
                var e = jQuery(this);
                return (
                  jQuery("#ajax-link").remove(),
                  jQuery("body").append('<div id="ajax-link"></div>'),
                  jQuery("#ajax-link").load(e.attr("href"), function () {
                    jQuery("#ajax-link").dialog({
                      autoOpen: !0,
                      width: e.data("popup-width") || 800,
                      height: e.data("popup-height") || 600,
                      closeOnEscape: !0,
                      title: e.attr("title"),
                      modal: !0,
                    });
                  }),
                  !1
                );
              }),
              jQuery(".admin-menu").adminMenu(
                (0, n.amVar)("cActiveMenuID", (0, n.amVar)("vActiveMenuID"))
              ),
              k(),
              jQuery(document).ajaxComplete(function () {
                setTimeout(k, 0);
              });
            var l = jQuery(".am-errors:visible:first,.am-error:visible:first");
            l.length &&
              jQuery("html, body").scrollTop(Math.floor(l.offset().top)),
              (0, n.amVar)("admin-user-invoices-data") &&
                w((0, n.amVar)("admin-user-invoices-data")),
              null ===
                (i = document.querySelector(
                  ".input-lookup-input .input-lookup-empty"
                )) ||
                void 0 === i ||
                i.addEventListener("click", function (e) {
                  var t = document.querySelector(".input-lookup-input input");
                  (t.value = ""),
                    t.dispatchEvent(new Event("change")),
                    t.focus();
                });
            var s = document.querySelector(".input-lookup-input input"),
              c = document.querySelector(".input-lookup"),
              d = document.querySelector(".input-lookup-button");
            c &&
              (["keyup", "change"].forEach(function (e) {
                s.addEventListener(e, function (e) {
                  c.classList.toggle("state-has-value", 0 !== s.value.length);
                });
              }),
              ["mouseover", "mouseout"].forEach(function (e) {
                d.addEventListener(e, function (e) {
                  c.classList.toggle("hover");
                });
              }),
              s.addEventListener("focusin", function (e) {
                c.classList.add("state-open"),
                  document
                    .getElementById("user-lookup")
                    .setAttribute(
                      "placeholder",
                      (0, o.S)("login, email or name")
                    );
              }),
              s.addEventListener("focusout", function (e) {
                0 != s.value.length || c.classList.remove("state-open"),
                  0 != s.value.length ||
                    document
                      .getElementById("user-lookup")
                      .setAttribute("placeholder", (0, o.S)("Find an user…"));
              }));
          });
        var Q = !1;
        function x(e, t, a) {
          var n = document.getElementById("flash-message");
          n ||
            (((n = document.createElement("div")).id = "flash-message"),
            document.body.appendChild(n));
          var i,
            o,
            r = document.createElement("div");
          (r.className = t),
            (r.style.display = "none"),
            (r.innerHTML = e),
            n.appendChild(r),
            ((i = r).style.opacity = 0),
            (i.style.display = o || "block"),
            (function e() {
              var t = parseFloat(i.style.opacity);
              (t += 0.1) > 1 ||
                ((i.style.opacity = t), requestAnimationFrame(e));
            })(),
            a &&
              setTimeout(function () {
                !(function (e) {
                  var t =
                    arguments.length > 1 && void 0 !== arguments[1]
                      ? arguments[1]
                      : null;
                  (e.style.opacity = 1),
                    (function a() {
                      (e.style.opacity -= 0.1) < 0
                        ? ((e.style.display = "none"),
                          t && e.parentNode.removeChild(e))
                        : requestAnimationFrame(a);
                    })();
                })(r, !0);
              }, a);
        }
        jQuery(document).tooltip({
          items: "[data-tooltip], [data-tooltip-url]",
          show: !1,
          hide: !1,
          content: function (e) {
            var t = this;
            Q = setTimeout(function () {
              jQuery(t).data("tooltip-url")
                ? (jQuery(t).data("tooltip") && e(jQuery(t).data("tooltip")),
                  jQuery.get(jQuery(t).data("tooltip-url"), function (a) {
                    jQuery(t).data("tooltip", a), e(a);
                  }))
                : e(jQuery(t).data("tooltip"));
            }, 350);
          },
          open: function (e, t) {
            jQuery("[id^=ui-tooltip]").not(t.tooltip[0]).hide(),
              jQuery(t.tooltip[0]).addClass("ui-tooltip-shown");
          },
          position: {
            my: "left top+35",
            at: "left bottom",
            using: function (e, t) {
              $(this).css(e),
                $(this).addClass("ui-tooltip-position-vertical-" + t.vertical),
                $(this).addClass(
                  "ui-tooltip-position-horizontal-" + t.horizontal
                );
            },
          },
        }),
          jQuery(window).blur(function () {
            jQuery("[data-tooltip], [data-tooltip-url]").blur();
          }),
          jQuery(document).on(
            "mouseleave",
            "[data-tooltip], [data-tooltip-url]",
            function () {
              Q && (clearTimeout(Q), (Q = !1));
            }
          ),
          jQuery(document).on("blur", ".input_datetime-time", function () {
            var e = $(this)
              .val()
              .replace(/[^0-9]/g, "")
              .substr(0, 4);
            (e += "0000".substr(0, 4 - e.length)),
              $(this).val(e.substr(0, 2) + ":" + e.substr(2, 2));
          }),
          (window.flashError = function (e) {
            return x(e, "error", 5e3);
          }),
          (window.flashMessage = function (e) {
            return x(e, "message", 2500);
          }),
          (window.destroyCkeditor = function (e) {
            window.configDisable_rte || CKEDITOR.instances[e].destroy();
          }),
          (window.initCkeditor = function (e, t) {
            if (!window.configDisable_rte) {
              var a = null;
              (t = t || {}).placeholder_items &&
                (a = { name: "amember", items: ["CreatePlaceholder"] });
              var n = [];
              n.push({
                name: "basicstyles",
                items: ["Bold", "Italic", "Strike", "-", "RemoveFormat"],
              }),
                a && n.push(a),
                n.push({
                  name: "paragraph",
                  items: [
                    "NumberedList",
                    "BulletedList",
                    "-",
                    "Outdent",
                    "Indent",
                    "-",
                    "Blockquote",
                    "CreateDiv",
                    "-",
                    "JustifyLeft",
                    "JustifyCenter",
                    "JustifyRight",
                  ],
                }),
                n.push({
                  name: "insert",
                  items: [
                    "Link",
                    "Unlink",
                    "Image",
                    "Youtube",
                    "MediaEmbed",
                    "Table",
                    "HorizontalRule",
                    "PageBreak",
                  ],
                }),
                n.push({
                  name: "tools",
                  items: ["Maximize", "Source", "Templates", "SpellChecker"],
                }),
                n.push({
                  name: "clipboard",
                  items: [
                    "Cut",
                    "Copy",
                    "Paste",
                    "PasteText",
                    "PasteFromWord",
                    "-",
                    "Undo",
                    "Redo",
                  ],
                }),
                n.push("/"),
                n.push({
                  name: "styles",
                  items: [
                    "Styles",
                    "Format",
                    "Font",
                    "FontSize",
                    "TextColor",
                    "BGColor",
                  ],
                }),
                CKEDITOR.plugins.addExternal(
                  "placeholder",
                  amUrl(
                    "/application/default/views/public/js/ckeditor/plugins/placeholder/"
                  ),
                  "plugin.js"
                ),
                CKEDITOR.plugins.addExternal(
                  "youtube",
                  amUrl(
                    "/application/default/views/public/js/ckeditor/plugins/youtube/"
                  ),
                  "plugin.js"
                );
              var i = {
                extraPlugins: "placeholder,youtube",
                autoGrow_maxHeight: 800,
                baseHref: amUrl(""),
                customConfig: !1,
                language: window.lang,
                toolbar: "Am",
                toolbar_Am: n,
                allowedContent: !0,
                autoParagraph: !1,
                fullPage: !0,
                fillEmptyBlocks: !1,
                baseFloatZIndex: 1e5,
                on: {
                  beforeSetMode: function (e) {
                    e.editor.config.fullPage = /<html/.test(e.editor.getData());
                  },
                },
              };
              return CKEDITOR.replace(e, jQuery.extend(i, t));
            }
          }),
          (window.initDatepicker = function (e, t) {
            return jQuery(e || "input.datepicker").datepicker(
              jQuery.extend(
                {
                  defaultDate: window.uiDefaultDate,
                  dateFormat: window.uiDateFormat,
                  firstDay: window.uiWeekFirstDay || 0,
                  constrainInput: !0,
                  changeMonth: !0,
                  changeYear: !0,
                  shortYearCutoff: 37,
                  yearRange: "c-90:c+10",
                  showButtonPanel: !0,
                  beforeShow: function (e) {
                    setTimeout(function () {
                      var t = jQuery(e)
                        .datepicker("widget")
                        .find(".ui-datepicker-buttonpane");
                      jQuery("<button>", {
                        text: "Lifetime",
                        click: function () {
                          jQuery(e).datepicker(
                            "setDate",
                            new Date(2037, 11, 31, 1, 0, 0)
                          );
                        },
                      })
                        .appendTo(t)
                        .addClass(
                          "ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all"
                        );
                    }, 1);
                  },
                  onChangeMonthYear: function (e, t, a) {
                    setTimeout(function () {
                      var e = jQuery(a)
                        .datepicker("widget")
                        .find(".ui-datepicker-buttonpane");
                      jQuery("<button>", {
                        text: "Lifetime",
                        click: function () {
                          jQuery(input).datepicker(
                            "setDate",
                            new Date(2037, 11, 31, 1, 0, 0)
                          );
                        },
                      })
                        .appendTo(e)
                        .addClass(
                          "ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all"
                        );
                    }, 1);
                  },
                },
                t || {}
              )
            );
          }),
          jQuery.widget("ui.dialog", jQuery.ui.dialog, {
            _allowInteraction: function (e) {
              return (
                !!this._super(e) ||
                e.target.ownerDocument != this.document[0] ||
                !!jQuery(e.target).closest(".cke_dialog").length ||
                !!jQuery(e.target).closest(".cke").length ||
                !!jQuery(e.target).closest(".select2-container").length ||
                void 0
              );
            },
            _moveToTop: function (e, t) {
              (e && this.options.modal) || this._super(e, t);
            },
          }),
          (jQuery.fn.liveEdit = function (e, t) {
            var a = jQuery.extend(
              { divEditable: !0, input: "<input type=text>" },
              e
            );
            return this.each(function () {
              var e = jQuery(this),
                n = jQuery("<div class='editable'>");
              a.divEditable && e.before(n),
                e.wrap("<span>"),
                e.click(function (i) {
                  var o = jQuery(this).parent();
                  if (!e.hasClass("opened")) {
                    e.addClass("opened");
                    var r = e.text(),
                      l = jQuery(a.input).val(r);
                    o.removeClass("editable"),
                      e.hide(),
                      n.hide(),
                      o.append(l),
                      l.focus().select(),
                      setTimeout(function () {
                        l.on("outerClick keydown", function (a) {
                          if ("keydown" != a.type || 13 == a.keyCode) {
                            var i = e.text(),
                              o = l.val();
                            e.text(o).show(),
                              n.show(),
                              e.removeClass("opened"),
                              l.remove(),
                              t && t.call(e.get(0), o, i);
                          }
                        });
                      }, 5);
                  }
                });
            });
          }),
          (window.setCookie = function (e, t) {
            var a = new Date(),
              n = new Date();
            n.setTime(a.getTime() + 31536e6),
              (document.cookie =
                e +
                "=" +
                escape(t) +
                "; path=/; expires=" +
                n.toGMTString() +
                "; SameSite=Lax");
          }),
          (window.getCookie = function (e) {
            var t = e + "=",
              a = document.cookie.indexOf(t);
            if (-1 == a) return null;
            var n = document.cookie.indexOf(";", a + t.length);
            return (
              -1 == n && (n = document.cookie.length),
              unescape(document.cookie.substring(a + t.length, n))
            );
          });
      },
      61514: function (e, t, a) {
        "use strict";
        a.r(t);
        a(69826),
          a(41539),
          a(74916),
          a(15306),
          a(83753),
          a(47042),
          a(56977),
          a(73210),
          a(82526),
          a(41817),
          a(32165),
          a(66992),
          a(78783),
          a(33948);
        function n(e) {
          return (
            (n =
              "function" == typeof Symbol && "symbol" == typeof Symbol.iterator
                ? function (e) {
                    return typeof e;
                  }
                : function (e) {
                    return e &&
                      "function" == typeof Symbol &&
                      e.constructor === Symbol &&
                      e !== Symbol.prototype
                      ? "symbol"
                      : typeof e;
                  }),
            n(e)
          );
        }
        var i = a(30381),
          o = a(38965).amVar;
        !(function (e) {
          function t() {
            var t = [];
            return (
              e(".billing-plan").each(function () {
                var a = e(this),
                  n = a.prop("id").replace(/^plan-/, "");
                if ("TPL" != n) {
                  var i = {
                    id: n,
                    title: a.find(".plan-title-edit").val(),
                    first_price: a.find(':input[name*="first_price"]').val(),
                    second_price: a.find(':input[name*="second_price"]').val(),
                    rebill_times: a.find('select[name*="_rebill_times"]').val(),
                    currency: a.find("select[name$='[currency]']").val(),
                  };
                  t.push(i);
                }
              }),
              t
            );
          }
          (window.onBillingTitleKeypressHandler = function (t) {
            if (13 == t.keyCode) {
              t.stopImmediatePropagation(),
                t.preventDefault(),
                e(this)
                  .closest("legend")
                  .find(".plan-title-text")
                  .text(e(this).val()),
                e(this).hide(),
                e(this).closest("legend").find(".plan-title-text").show(),
                e("body").off("click.inplace-edit");
              var a = e(this),
                n = a.closest(".billing-plan").attr("id");
              e("#billing-plan-wrap ul a[href='#" + n + "']").text(a.val());
            }
          }),
            (jQuery.fn.billingPlan = function () {
              return this.each(function () {
                var t = this,
                  a = e(this)
                    .attr("id")
                    .replace(/^plan-/, "");
                if ("TPL" !== a) {
                  e(this).find(".magicselect").restoreMagicSelect(),
                    e(this).data("id", a),
                    e("legend", this).append(
                      ' : <span class="terms-text"></span>'
                    );
                  var n = e("select[name$='[currency]']", this),
                    o = e("input[name$='[first_price]']", this),
                    r = e("input[name$='[first_period][c]']", this),
                    l = e("select[name$='[first_period][u]']", this),
                    s = e("input[name$='[rebill_times]']", this),
                    c = e("select[name$='[_rebill_times]']", this),
                    d = e("input[name$='[second_price]']", this),
                    u = e("input[name$='[second_period][c]']", this),
                    p = e("select[name$='[second_period][u]']", this);
                  n
                    .change(function () {
                      e(
                        ".billing-plan-currency",
                        e(this).closest("fieldset")
                      ).text(e(this).val());
                    })
                    .change(),
                    n.select2({
                      minimumResultsForSearch: 10,
                      width: "resolve",
                    }),
                    s.change(function () {
                      e(this).val() >= 1
                        ? e(u).add(d).parents(".am-row").show()
                        : e(u).add(d).parents(".am-row").hide();
                    }),
                    c
                      .change(function () {
                        var t = e(this),
                          a = t.val(),
                          n = s;
                        "x" == a
                          ? (n.show(),
                            null != n.data("saved_value") &&
                              n.val(n.data("saved_value")))
                          : ("x" == t.data("saved_value") &&
                              n.data("saved_value", n.val()),
                            n.val(a),
                            n.hide()),
                          t.data("saved_value", t.val()),
                          n.change();
                      })
                      .change(),
                    l
                      .change(function () {
                        var t = e(this).val();
                        "lifetime" == t ? r.hide().val("2037-12-31") : r.show(),
                          "lifetime" != t &&
                            "2037-12-31" == r.val() &&
                            r.val(""),
                          "lifetime" == t || "fixed" == t
                            ? c.val("0").change().parents(".am-row").hide()
                            : (c.parents(".am-row").show(),
                              "" == d.val() &&
                                "" == u.val() &&
                                (d.val(o.val()),
                                u.val(r.val()),
                                p.val(l.val())));
                      })
                      .change(),
                    p
                      .change(function () {
                        var t = e(this).val();
                        "lifetime" == t ? u.hide() : u.show(),
                          "lifetime" != t
                            ? (c
                                .find('option[value="x"],option[value="99999"]')
                                .prop("disabled", !1),
                              "2037-12-31" == u.val() && u.val(""))
                            : (c.val("1").change(),
                              c
                                .find('option[value="x"],option[value="99999"]')
                                .prop("disabled", !0));
                      })
                      .change(),
                    e(".plan-title-edit", this).on(
                      "keypress",
                      window.onBillingTitleKeypressHandler
                    ),
                    (t.getPeriodText = function (e, t, a) {
                      var n;
                      switch (t) {
                        case "d":
                          n = 1 == e ? "day" : "days";
                          break;
                        case "m":
                          n = 1 == e ? "month" : "months";
                          break;
                        case "y":
                          n = 1 == e ? "year" : "years";
                          break;
                        case "fixed":
                          return (
                            " up to " + i(e).format(window.momentDateFormat)
                          );
                      }
                      var o = e;
                      return 1 == e && (o = a ? "" : "one"), o + " " + n;
                    }),
                    (t.calculateTerms = function () {
                      var e = this.getValues();
                      if (!e.first_price.length && !e.first_period_c.length)
                        return "&ndash;";
                      var t =
                          /[0-9]{4}-[0-9]{2}-[0-9]{2}/.test(e.first_period_c) &&
                          "d" == e.first_period_u,
                        a = parseFloat(e.first_price),
                        n = t ? e.first_period_c : parseInt(e.first_period_c),
                        i = t ? "fixed" : e.first_period_u,
                        o = parseInt(
                          "x" == e._rebill_times
                            ? e.rebill_times
                            : e._rebill_times
                        ),
                        r = parseFloat(e.second_price),
                        l = parseInt(e.second_period_c),
                        s = e.second_period_u,
                        c = e.currency,
                        d = a + " " + c;
                      a <= 0 && (d = "Free");
                      var u = r + " " + c;
                      r <= 0 && (u = "free");
                      var p = d;
                      return (
                        "lifetime" != i &&
                          (p += o
                            ? " for first " + this.getPeriodText(n, i, !0)
                            : ("fixed" == i ? " " : " for ") +
                              this.getPeriodText(n, i)),
                        o &&
                          ("lifetime" == s
                            ? (p += ", then " + u + " for lifetime")
                            : ((p +=
                                ", then " +
                                u +
                                " for every " +
                                this.getPeriodText(l, s)),
                              o < 9999 &&
                                (p += ", for " + o + " installments"))),
                        p.replace(/[ ]+/g, " ")
                      );
                    }),
                    (t.getValues = function () {
                      var t = {};
                      return (
                        e("[name]:input", this).each(function () {
                          var a = e(this);
                          t[
                            a
                              .attr("name")
                              .replace(/_plan\[.+?\]\[/, "")
                              .replace(/\]$/, "")
                              .replace(/\]\[/, "_")
                          ] =
                            "string" == typeof a.val()
                              ? a
                                  .val()
                                  .replace(/^[ ]+/, "")
                                  .replace(/[ ]+$/, "")
                              : a.val();
                        }),
                        t
                      );
                    }),
                    e([n[0], o[0], r[0], l[0], s[0], c[0], d[0], u[0], p[0]])
                      .change(function () {
                        var a = t.calculateTerms();
                        e(".terms-text", t).html(a),
                          e(
                            "#billing-plan-wrap ul a[href='#" +
                              e(t).prop("id") +
                              "']"
                          ).prop("title", a);
                      })
                      .change();
                }
              });
            }),
            e(document).on("click", "a.billing-plan-del", function (t) {
              var a = e(this).closest("li").data("plan_id");
              e(".billing-plan").length <= 2
                ? alert(
                    "You cannot delete last billing plan. Please add another billing plan first"
                  )
                : confirm(
                    "Are you sure you want to remove this billing plan?"
                  ) &&
                  (e("#billing-plan-wrap").tabs("option", "active", 0),
                  e("#plan-" + a).remove(),
                  e("#billing-plan-wrap a[href='#plan-" + a + "']")
                    .closest("li")
                    .remove(),
                  e("#billing-plan-wrap").tabs("refresh"),
                  t.stopPropagation());
            }),
            e(document).on("click", ".plan-add", function (t) {
              var a = new Date().getTime(),
                n = e("#plan-TPL")
                  .html()
                  .replace(/TPL/g, a)
                  .replace(/TEMPLATE/g, "New Billing Plan");
              e("#billing-plan-wrap .plan-add")
                .closest("li")
                .before(
                  "<li id='tab-plan-" +
                    a +
                    "' data-plan_id='" +
                    a +
                    "'><a href='#plan-" +
                    a +
                    "'><span>New Billing Plan</span></a><a href='javascript:;' class='billing-plan-del' title='Delete Billing Plan'>&#10005;</a></li>"
                ),
                e("#billing-plan-wrap").tabs("refresh"),
                e("#plan-TPL").after(
                  '<fieldset class="billing-plan" id="plan-' +
                    a +
                    '">' +
                    n +
                    "</fieldset>"
                ),
                e("#plan-" + a + " .plan-title-text").click(),
                e("#plan-" + a + " .plan-title-edit")
                  .focus()
                  .select(),
                e("#plan-" + a + " .plan-title-edit").on(
                  "keypress",
                  window.onBillingTitleKeypressHandler
                ),
                e("#plan-" + a).billingPlan(),
                e("#billing-plan-wrap").tabs("option", "active", -2),
                e("#billing-plan-wrap .ui-tabs-nav").sortable("refresh"),
                e("#billing-plan-wrap .ui-tabs-nav").sortable(
                  "option",
                  "update"
                )(),
                t.stopImmediatePropagation();
            }),
            e(function () {
              e(document).on(
                "change",
                ".billing-plan .variable_qty",
                function () {
                  var t = e(this)
                    .parents(".am-element")
                    .find("input[type='text']");
                  this.checked
                    ? t.val(1).prop("readonly", "readonly").css("color", "gray")
                    : t.prop("readonly", null).css("color", "black");
                }
              ),
                e(".billing-plan .variable_qty").change();
            }),
            e(document).on("click", ".plan-title-text", function (t) {
              var a = e(this),
                n = a.parents("legend").find(".plan-title-edit");
              a.hide(),
                n.show().focus().select(),
                t.stopPropagation(),
                e("body").on("click.inplace-edit", function (t) {
                  if (!e(t.target).is(".plan-title-edit")) {
                    a.text(n.val()),
                      n.hide(),
                      a.show(),
                      e("body").off("click.inplace-edit");
                    var i = a.closest(".billing-plan").attr("id");
                    e("#billing-plan-wrap ul a[href='#" + i + "']").text(
                      n.val()
                    );
                  }
                });
            }),
            jQuery(document).ready(function (e) {
              e(".billing-plan").billingPlan(),
                e("#row-am-product-option-group-TPL").productOptions(),
                e(".am-product-option-add").click(function () {
                  e(this).productOptions("add");
                }),
                e("input[name='start_date_fixed']").prop(
                  "disabled",
                  "disabled"
                ),
                e("select[name='renewal_group']")
                  .prop("id", "renewal_group")
                  .after(
                    e(
                      "<span> <a href='javascript:' id='add-renewal-group' class='local'>add group</a></span>"
                    )
                  ),
                e("select[name='renewal_group']")
                  .change(function () {
                    e(this).toggle(e(this).find("option").length > 1);
                  })
                  .change(),
                e("#start-date-edit").magicSelect({
                  callbackTitle: function (t) {
                    var a = t.text;
                    if ("fixed" == t.value) {
                      var n = e("input[name='start_date_fixed']");
                      a +=
                        "&nbsp;" +
                        e("<p></p>")
                          .append(
                            n
                              .clone()
                              .prop("disabled", "")
                              .show()
                              .prop("id", "start_date_fixed")
                              .removeClass("hasDatepicker")
                          )
                          .html();
                    }
                    return a;
                  },
                }),
                e(document).on("click", "a#add-renewal-group", function () {
                  var t = prompt(
                    "Enter title for your new renewal group, for example: group#1",
                    ""
                  );
                  t &&
                    e("select#renewal_group")
                      .append(e("<option></option>").val(t).html(t))
                      .val(t)
                      .change();
                }),
                e(document).on(
                  "focus",
                  "input[name='start_date_fixed']",
                  function () {
                    e(this).hasClass("hasDatepicker") ||
                      e(this).datepicker({
                        defaultDate: window.uiDefaultDate,
                        dateFormat: window.uiDateFormat,
                        firstDay: window.uiWeekFirstDay || 0,
                        changeMonth: !0,
                        changeYear: !0,
                      });
                  }
                );
            });
          var a,
            r = {
              init: function () {
                return (
                  this.each(function () {
                    var t = e(this);
                    t.data("product-option");
                    if (
                      (t.addClass("am-product-option"),
                      t
                        .closest("fieldset")
                        .sortable({ items: ".am-product-option" }),
                      t.find("a.am-product-option-delete").click(function () {
                        confirm("Really delete?") &&
                          e(this).closest(".am-product-option").remove();
                      }),
                      t.find("select.option-type").change(function () {
                        var t = e(this).val(),
                          a =
                            "select" == t ||
                            "multi_select" == t ||
                            "radio" == t ||
                            "checkbox" == t;
                        e(this)
                          .closest(".am-product-option")
                          .find(".edit-options")
                          .toggle(a);
                      }),
                      t.find(".edit-options").click(function () {
                        e(this)
                          .closest(".am-product-option")
                          .productOptions("editOptions");
                      }),
                      t.find(".edit-desc").click(function () {
                        e(this)
                          .closest(".am-product-option")
                          .productOptions("editDesc");
                      }),
                      "row-am-product-option-group-TPL" == this.id)
                    ) {
                      if (a) return;
                      (a = t),
                        t.closest("form").submit(function () {
                          return e(this).productOptions("beforeSubmit");
                        }),
                        t.hide(),
                        t.productOptions("expandValues", t);
                    }
                  }),
                  this
                );
              },
              add: function () {
                var t = a.clone(!0).attr("id", null);
                return (
                  e(this)
                    .closest("fieldset")
                    .find(".am-product-option:last")
                    .after(t),
                  o("langCount") > 1 &&
                    t.find('input[data-field="title"]').translate(),
                  t.show(),
                  this
                );
              },
              beforeSubmit: function () {
                return (
                  e(this).productOptions("collectValues"),
                  !e(this).productOptions("validate")
                );
              },
              validate: function () {
                var t = !1;
                return (
                  e(this)
                    .find(".am-product-option")
                    .not("#row-am-product-option-group-TPL")
                    .each(function () {
                      var a = e(this).find('input[data-field="title"]');
                      "" == a.val() &&
                        (a
                          .prop("placeholder", "Title is required!")
                          .get(0)
                          .focus(),
                        (t = !0));
                      var n = e(this).find('select[data-field="type"]');
                      "" == n.val() && (n.get(0).focus(), (t = !0));
                    }),
                  t
                );
              },
              collectValues: function () {
                var t = e("input#am-product-options-hidden"),
                  a = { options: [] };
                e(this)
                  .find(".am-product-option")
                  .not("#row-am-product-option-group-TPL")
                  .each(function () {
                    var t = e(this),
                      n = {};
                    t.find(":input").each(function () {
                      n[e(this).data("field")] =
                        "checkbox" == this.type
                          ? this.checked
                            ? 1
                            : 0
                          : e(this).val();
                    }),
                      a.options.push(n);
                  }),
                  t.val(e.toJSON(a));
              },
              expandValues: function (t) {
                var a = e("input#am-product-options-hidden"),
                  n = JSON.parse(a.val());
                for (var i in n.options) {
                  var o = n.options[i];
                  t.productOptions("add"),
                    e(".am-product-option")
                      .last()
                      .find(":input")
                      .each(function () {
                        "checkbox" == this.type
                          ? 1 == o[e(this).data("field")] &&
                            e(this).prop("checked", "checked")
                          : e(this).val(o[e(this).data("field")]),
                          e(this).change();
                      });
                }
              },
              editOptions: function () {
                var t = e(this).find(".option-options"),
                  a = e("<input type='hidden'>");
                e("#am-product-options-options").append(a);
                var n = e("#am-product-options-options").dialog({
                  autoOpen: !0,
                  height: 400,
                  width: "80%",
                  modal: !0,
                  title: "Edit Options",
                  buttons: {
                    OK: function () {
                      t.val(a.productOptionsEditor("updateValue").val()),
                        n.dialog("close");
                    },
                    Cancel: function () {
                      confirm("Discard changes?") && n.dialog("close");
                    },
                  },
                  close: function () {
                    a.remove(), e(this).find(".options-editor").remove();
                  },
                  open: function () {
                    a.val(t.val()),
                      a.val() || a.val(e.toJSON({ options: {}, default: [] })),
                      a.addClass("options-editor").productOptionsEditor();
                  },
                });
              },
              editDesc: function () {
                var t = e(this).find(".option-desc"),
                  a = e("<textarea class='am-el-wide'></textarea>");
                e("#am-product-options-options").append(a);
                var n = e("#am-product-options-options").dialog({
                  autoOpen: !0,
                  height: 200,
                  width: 400,
                  modal: !0,
                  title: "Product Option Description",
                  buttons: {
                    OK: function () {
                      t.val(a.val()), n.dialog("close");
                    },
                    Cancel: function () {
                      confirm("Discard changes?") && n.dialog("close");
                    },
                  },
                  close: function () {
                    a.remove(), e(this).empty();
                  },
                  open: function () {
                    a.val(t.val()), o("langCount") > 1 && a.translate();
                  },
                });
              },
            };
          (jQuery.fn.productOptions = function (e) {
            return r[e]
              ? r[e].apply(this, Array.prototype.slice.call(arguments, 1))
              : "object" !== n(e) && e
              ? void jQuery.error(
                  "Method " + e + " does not exist on jQuery.ngrid"
                )
              : r.init.apply(this, arguments);
          }),
            (function (e) {
              e.fn.productOptionsEditor = function (a) {
                return this.each(function () {
                  var n = this,
                    i = e(this);
                  if ("updateValue" == a) {
                    var o,
                      r = { options: [], default: [], prices: {} };
                    return (
                      (o = e(this).data("table"))
                        .find("tr.option")
                        .each(function () {
                          var t = e(this).find('span[data-field="key"]').text(),
                            a = e(this).find('span[data-field="label"]').text();
                          r.options.push([t, a]),
                            e(this)
                              .find('[data-field="default"]')
                              .prop("checked") && r.default.push(t),
                            (r.prices[t] = {}),
                            e(this)
                              .find(
                                "span[data-field$='_price'],a[data-field$='_price']"
                              )
                              .each(function () {
                                var a = e(this).data("plan");
                                r.prices[t][a] || (r.prices[t][a] = []);
                                var n = parseFloat(e(this).text());
                                isNaN(n) ||
                                  (r.prices[t][a][
                                    "first_price" == e(this).data("field")
                                      ? 0
                                      : 1
                                  ] = n);
                              });
                        }),
                      e(this).val(e.toJSON(r)),
                      this
                    );
                  }
                  if (!i.data("product-options-editor-init")) {
                    if ("hidden" != this.type)
                      throw new Error(
                        "Element should be hidden in order to use productOptionsEditor for it. [" +
                          this.type +
                          "] given."
                      );
                    i.data("product-options-editor-init", 1),
                      (r = JSON.parse(e(n).val())),
                      (r = e.extend(
                        { options: [], default: [], prices: {} },
                        r
                      )),
                      e(n).data("billing-plans", t()),
                      e("head").append(
                        "            <style type='text/css' id='hide-option-price'>\n                .no-price .option-option-price { display: none; }\n                .option-price-zero { font-size: 70%; opacity:.4; padding-left:.2em; }\n                .option-price-delimeter { opacity:.3; padding: 0 .2em; }\n            </style>"
                      ),
                      (r.default = r.default || []),
                      (o = e(
                        "<table>\n            <thead><tr>\n                <th title='Is Default?'>Def</th>\n                <th>Value</th>\n                <th>Label</th>\n                <th class='admin-product-option-option-add-wrapper'>&nbsp;</th>\n            </tr></thead><tbody>\n            \n            <tr class='new-option'>\n                <td><input type='checkbox' class='option-def' value=1></td>\n                <td><input type=text class='option-key' size=5></td>\n                <td><input type='text' class='option-label'></td>\n                <td class='admin-product-option-option-add-wrapper'><a href='javascript:;' class='button admin-product-option-option-add'>+</a></td>\n            </tr>\n            <tr class=''>\n                <td colspan=4><a href='javascript:;' class='admin-option-option-show-prices local'>Click to edit surcharge amounts (will be added if user selects the option)</a></td>\n            </tr>\n            </tbody></table>"
                      )),
                      i.before(o);
                    var l = e('<div class="options-editor no-price">');
                    o.wrap(l),
                      o
                        .find(".admin-product-option-option-add")
                        .click(function () {
                          var t = o.find(".new-option"),
                            a = t.find("input.option-key").val().trim(),
                            n = t.find("input.option-label").val(),
                            i = {},
                            r = !!t.find("input.option-def").prop("checked"),
                            l = !1;
                          if (
                            (t.find("input[data-plan]").each(function () {
                              var t = e(this).data("plan"),
                                a = e(this).data("field"),
                                n =
                                  "" != this.value ? parseFloat(this.value) : 0;
                              "" != this.value &&
                                isNaN(n) &&
                                (alert("Incorrect amount"),
                                this.focus(),
                                (l = !0)),
                                i[t] || (i[t] = [0, 0]),
                                (i[t]["first_price" == a ? 0 : 1] = n);
                            }),
                            l)
                          )
                            return;
                          if (a.length < 1)
                            return t
                              .find("input.option-key")
                              .prop("placeholder", "Enter key")
                              .get(0)
                              .focus();
                          if (n.length < 1)
                            return t
                              .find("input.option-label")
                              .prop("placeholder", "Label is required")
                              .get(0)
                              .focus();
                          var s = 0;
                          if (
                            (o.find('span[data-field="key"]').each(function () {
                              e(this).text() == a && (s = 1);
                            }),
                            s)
                          )
                            return (
                              alert("Key is not unqiue"),
                              void t.find("input.option-key").get(0).focus()
                            );
                          g(a, n, r, i),
                            t
                              .find(".option-key,.option-label")
                              .val("")
                              .prop("placeholder", ""),
                            t.find(".option-def").prop("checked", !1),
                            t.find("input[data-plan]").val("");
                        }),
                      i.data("table", o);
                    var s = o.find(".admin-option-option-show-prices");
                    s.click(function () {
                      o.closest(".options-editor").removeClass("no-price"),
                        e(this).closest("tr").remove();
                    });
                    var c = 0;
                    for (var d in (e.each(r.prices, function (e, t) {
                      var a;
                      for (a in t) (t[a][0] || t[a][1]) && c++;
                    }),
                    c &&
                      (o.closest(".options-editor").removeClass("no-price"),
                      s.closest("tr").remove()),
                    i.data("billing-plans", t()),
                    e.each(e(n).data("billing-plans"), function (e, t) {
                      var a = "0" != t.rebill_times,
                        n =
                          t.title.length > 20
                            ? t.title.substr(0, 24) + "&hellip;"
                            : t.title;
                      o
                        .find(
                          "thead tr th.admin-product-option-option-add-wrapper"
                        )
                        .before(
                          "<th class='option-option-price'>" + n + "</th>"
                        ),
                        o
                          .find(
                            "tr.new-option td.admin-product-option-option-add-wrapper"
                          )
                          .before(
                            "<td class='option-option-price' align='right'><input type='text' placeholder='+First' size='5' class='option-price' data-plan='" +
                              t.id +
                              "' data-field=first_price>" +
                              (a
                                ? "/<input type='text' placeholder='+Rebills' class='option-price' data-plan='" +
                                  t.id +
                                  "' data-field=second_price size='5'"
                                : "") +
                              "</td>"
                          );
                    }),
                    r.options)) {
                      var u = r.options[d][0],
                        p = r.options[d][1],
                        f = r.prices[u];
                      f || (f = {});
                      var h = !1;
                      for (var m in r.default) r.default[m] == u && (h = !0);
                      g(u, p, h, f);
                    }
                    o.find("tbody").sortable({ items: "tr.option" });
                  }
                  function g(t, a, n, r) {
                    var l = e(
                      "<tr class='option'>\n                            <td><input type='checkbox' data-field=default value=1></td>\n                            <td><span data-field=key></span></td>\n                            <td><span data-field=label></span></td>\n                            <td align=center class='admin-product-option-option-add-wrapper'><a href='javascript:;'  class='remove-option-option am-link-del' title='Delete'>&#10005;</a></td>\n                        </tr>"
                    );
                    o.find(".new-option").before(l),
                      l.find('span[data-field="key"]').text(t),
                      l.find('span[data-field="label"]').text(a).liveEdit(),
                      l
                        .find('input[data-field="default"]')
                        .prop("checked", !!n),
                      l.find(".remove-option-option").click(function () {
                        e(this).closest("tr").remove();
                      });
                    var s = function (t, a) {
                        var n,
                          i = parseFloat(t);
                        isNaN(i)
                          ? e(this).text(a)
                          : ((n =
                              0 == i
                                ? 0
                                : i == Math.round(i)
                                ? Math.round(i)
                                : i.toFixed(2)),
                            e(this)
                              .toggleClass("option-price-zero", 0 == i)
                              .text(n));
                      },
                      c = i.data("billing-plans");
                    for (var d in c) {
                      var u = c[d],
                        p = "0" != u.rebill_times,
                        f = u.id,
                        h = e(
                          "<td class='option-option-price' style='font-size:1.1rem' align='right'>"
                        ),
                        m = r[f] ? r[f] : [0, 0],
                        g = e(
                          "<a class='local' href='javascript:;' data-field='first_price'>"
                        )
                          .text(m[0])
                          .data("plan", f);
                      if (
                        (h.append(g),
                        h.append(
                          "<span class=option-price-zero>" +
                            u.currency +
                            "</span>"
                        ),
                        g.liveEdit(
                          { divEditable: !1, input: "<input size=5>" },
                          s
                        ),
                        s.call(g[0], g.text(), 0),
                        p)
                      ) {
                        var v = e(
                          "<a class='local' href='javascript:;' data-field='second_price'>"
                        )
                          .text(m[1])
                          .data("plan", f);
                        h.append("<span class=option-price-delimeter>/</span>"),
                          h.append(v),
                          h.append(
                            "<span class=option-price-zero>" +
                              u.currency +
                              "</span>"
                          ),
                          v.liveEdit(
                            { divEditable: !1, input: "<input size=5>" },
                            s
                          ),
                          s.call(v[0], v.text(), 0);
                      }
                      l.find(".admin-product-option-option-add-wrapper").before(
                        h
                      );
                    }
                  }
                });
              };
            })(jQuery);
        })(jQuery);
      },
      19997: function (e, t, a) {
        "use strict";
        a.r(t);
        a(83753),
          a(69826),
          a(41539),
          a(74916),
          a(15306),
          a(68309),
          a(92222),
          a(4723);
        var n = a(38965),
          i = {
            hidden: null,
            enabled: null,
            disabled: null,
            update: function () {
              var e = [];
              jQuery("#bricks-enabled .brick").each(function (t, a) {
                var n = jQuery(a),
                  i = { id: n.attr("id"), class: n.data("class") };
                n.data("config") && (i.config = n.data("config")),
                  n.data("hide") && (i.hide = n.data("hide")),
                  n.data("labels") && (i.labels = n.data("labels")),
                  n.data("alias") && (i.alias = n.data("alias")),
                  (i.container = n.parents("[class^='brick fieldset']").length
                    ? n.parents("[class^='brick fieldset']").attr("id")
                    : null),
                  e.push(i);
              }),
                this.hidden.val(jQuery.toJSON(e));
            },
            init: function () {
              (this.hidden = jQuery(
                'input[name="fields"]',
                jQuery("#bricks-enabled").parents("form")
              )),
                (this.enabled = jQuery("#bricks-enabled")),
                (this.disabled = jQuery("#bricks-disabled")),
                this.update();
            },
            getBrickConfigDiv: function (e) {
              return jQuery("div#config_" + e);
            },
            showConfigDialog: function (e, t) {
              var a = "#brick-config-" + e[0].id;
              jQuery(a).dialog({
                modal: !0,
                autoOpen: !0,
                title: t,
                width: Math.min(700, Math.round(0.7 * jQuery(window).width())),
                buttons: {
                  Ok: function (t) {
                    jQuery(this).dialog("close"),
                      jQuery(e).data(
                        "config",
                        jQuery(a + " form :input")
                          .not("[name='_save_']")
                          .serialize()
                      ),
                      i.update(),
                      flashMessage(
                        "Configuration updated successfully. Information will be saved to database once you press 'Enter' in main form."
                      );
                  },
                  Cancel: function (e) {
                    jQuery(this).dialog("close");
                  },
                },
                beforeClose: function (e, t) {
                  jQuery(".html-editor", e.target).each(function (e, t) {
                    destroyCkeditor(jQuery(t).prop("id"));
                  });
                },
                close: function (e, t) {
                  jQuery(this).dialog("destroy");
                },
                create: function (e, t) {
                  jQuery(".html-editor", e.target).each(function (e, t) {
                    initCkeditor(
                      jQuery(t).prop("id"),
                      jQuery(t).data("options") || {}
                    );
                  });
                },
              });
            },
            showLabelDialog: function (e, t) {
              var a = jQuery("#brick-labels")
                .clone()
                .attr("id", "brick-labels-live");
              a.appendTo("body");
              var o = e.data("stdlabels"),
                r = e.data("labels"),
                l = a.find("textarea"),
                s = l.closest(".am-row");
              for (var c in o) {
                var d = l.closest(".am-row").clone(),
                  u = d.find("textarea");
                u
                  .attr({ id: "txt-" + c, name: c, placeholder: "Default" })
                  .data("stdlabel", o[c])
                  .text(r[c] ? r[c] : ""),
                  r[c] && u.addClass("changed"),
                  u.change(function (e) {
                    jQuery(this).toggleClass(
                      "changed",
                      "" != jQuery(this).val()
                    );
                  }),
                  r[c] && u.addClass("custom-label"),
                  d
                    .find(".am-element-title")
                    .html(
                      jQuery("<div />")
                        .text(c)
                        .html()
                        .replace(/\r?\n/, "<br />")
                    ),
                  s.after(d);
              }
              (0, n.amVar)("langCount") > 1 && a.find("textarea").translate(),
                s.remove(),
                a.dialog({
                  modal: !0,
                  autoOpen: !0,
                  title: t,
                  width: Math.min(
                    700,
                    Math.round(0.7 * jQuery(window).width())
                  ),
                  buttons: {
                    Ok: function (t) {
                      var n = {};
                      jQuery.each(
                        jQuery("textarea.changed", a).serializeArray(),
                        function (e, t) {
                          n[t.name] = t.value;
                        }
                      ),
                        e.data("labels", n);
                      var o = !1;
                      for (var r in n) n.hasOwnProperty(r) && (o = !0);
                      e.find("a.labels").toggleClass("custom-labels", o),
                        i.update(),
                        flashMessage(
                          "Configuration updated successfully. Information will be saved to database once you press 'Enter' in main form."
                        ),
                        jQuery(this).dialog("close").dialog("destroy"),
                        a.remove();
                    },
                    Cancel: function (e) {
                      jQuery(this).dialog("close").dialog("destroy"),
                        a.remove();
                    },
                  },
                });
            },
            showAliasDialog: function (e, t) {
              console.log(t);
              var a = jQuery("#brick-alias")
                .clone()
                .attr("id", "brick-alias-live");
              a.appendTo("body");
              var n = e.data("alias");
              a.find("[type=text]").val(n),
                a.dialog({
                  modal: !0,
                  title: "Brick Alias (for admin reference)",
                  autoOpen: !0,
                  width: Math.min(
                    700,
                    Math.round(0.7 * jQuery(window).width())
                  ),
                  buttons: {
                    Ok: function (n) {
                      var o = jQuery("[type=text]", a).val();
                      e.data("alias", o),
                        t.find(".brick-alias-alias").text(o),
                        t
                          .find(".brick-alias")
                          .prop(
                            "title",
                            "".concat(o, " (").concat(e.data("title"), ")")
                          ),
                        t.toggleClass("brick-has-alias", o.length > 0),
                        i.update(),
                        flashMessage(
                          "Configuration updated successfully. Information will be saved to database once you press 'Enter' in main form."
                        ),
                        jQuery(this).dialog("close").dialog("destroy"),
                        a.remove();
                    },
                    Cancel: function (e) {
                      jQuery(this).dialog("close").dialog("destroy"),
                        a.remove();
                    },
                  },
                });
            },
          };
        jQuery(document).ready(function (e) {
          var t = this;
          i.init(),
            (function t() {
              e("#bricks-enabled, #bricks-disabled, .fieldset-fields")
                .sortable({
                  connectWith: ".connectedSortable",
                  placeholder: "brick-editor-placeholder",
                  stop: function (e, a) {
                    t();
                  },
                })
                .disableSelection();
            })();
          var a = 1;
          e("#bricks-enabled .fieldset-fields").on(
            "sortreceive",
            function (t, a) {
              var n = e(a.item[0]);
              (n.hasClass("page-separator") || n.hasClass("fieldset")) &&
                (e(a.sender).sortable("cancel"), t.stopPropagation());
            }
          ),
            e("#bricks-enabled").on("sortreceive", function (t, n) {
              var i = e(n.sender[0]);
              if (
                "bricks-enabled" != i[0].id &&
                !i.hasClass("fieldset-fields")
              ) {
                var o,
                  r = e(n.item[0]),
                  l = n.item[0].id;
                if ((o = r.attr("id").match(/^(.+)-(\d+)$/))) {
                  var s = r.data("class"),
                    c = +o[2];
                  do {
                    c++;
                  } while (e("#" + s + "-" + c).length);
                  var d = s + "-" + c;
                  r.attr("id", d);
                  var u = r.clone().attr("id", l);
                  e("#bricks-disabled").append(u);
                  var p = e("#brick-config-" + l),
                    f = p.clone().attr("id", "brick-config-" + d);
                  f.find(".html-editor").each(function (e, t) {
                    jQuery(t).attr("id", jQuery(t).prop("id") + "-" + a++);
                  }),
                    p.after(f),
                    f.find(".magicselect").restoreMagicSelect(),
                    f.find(".magicselect-sortable").restoreMagicSelect(),
                    f.find(".select2").remove();
                  var h = { minimumResultsForSearch: 10, width: "300px" };
                  f.find("select.am-combobox-fixed")
                    .select2(h)
                    .data("select2-option", h);
                }
              }
            }),
            e("#bricks-disabled").on("sortreceive", function (t, a) {
              var n = e(a.item[0]);
              n.data("multiple") &&
                (e(a.sender).sortable("cancel"),
                a.item.find(".brick").each(function () {
                  e("#brick-config-" + jQuery(this).attr("id")).remove(),
                    jQuery(this).appendTo("#bricks-disabled");
                }),
                e("#brick-config-" + n.attr("id")).remove(),
                a.item.remove());
            }),
            e("#bricks-enabled").on("sortupdate", function (e, t) {
              i.update();
            }),
            e(document).on(
              "click touchstart",
              "#bricks-enabled a.configure",
              function (a) {
                return i.showConfigDialog(
                  e(a.target).closest(".brick"),
                  e(t).attr("title")
                );
              }
            ),
            e(document).on(
              "click touchstart",
              "#bricks-enabled a.labels",
              function (a) {
                return i.showLabelDialog(
                  e(a.target).closest(".brick"),
                  e(t).attr("title")
                );
              }
            ),
            e(document).on(
              "click touchstart",
              "#bricks-enabled .brick-head",
              function (t) {
                return i.showAliasDialog(
                  e(t.target).closest(".brick"),
                  e(t.target).closest(".brick-head")
                );
              }
            ),
            e(".hide-if-logged-in input[type='checkbox']").click(function () {
              e(this)
                .closest(".brick")
                .data("hide", this.checked ? "1" : "0"),
                i.update();
            });
        }),
          (window.bricksEditor = i);
      },
      68287: function (e, t, a) {
        "use strict";
        a.r(t);
        a(83753),
          a(41539),
          a(39714),
          a(40561),
          a(69826),
          a(73210),
          a(2707),
          a(26699),
          a(32023);
        var n = a(60190);
        !(function (e) {
          e.fn.optionsEditor = function (t) {
            return this.each(function () {
              var a,
                i,
                o,
                r,
                l = e(this),
                s = e("<tr></tr>"),
                c = e("<td></td>"),
                d = e("<th></th>");
              if (!l.data("initialized")) {
                if ("hidden" != this.type)
                  throw new Error(
                    "Element should be hidden in order to use optionsEditor for it. [" +
                      this.type +
                      "] given."
                  );
                l.data("initialized", 1);
                e.extend({}, t);
                !(function () {
                  if (
                    (((a = JSON.parse(l.val())).default = a.default || []),
                    (a.order = a.order || []),
                    e.isArray(a.options))
                  ) {
                    for (var t = new Object(), u = 0; u < a.options.length; u++)
                      t[u] = a.options[u];
                    a.options = t;
                  }
                  var h = e("<table></table>"),
                    m = s.clone();
                  (o = e(
                    '<input type="text" style="width:100%; box-sizing:border-box" />'
                  )),
                    (i = e('<input type="text" />').attr("size", 5)),
                    (r = e('<input type="checkbox" />'));
                  var g = s.clone();
                  g
                    .append(
                      d.clone().append("Default").attr("title", "Is Default?")
                    )
                    .append(d.clone().append("Value"))
                    .append(d.clone().append("Label"))
                    .append(d.clone().append("&nbsp;")),
                    i.change(function () {
                      e(this).val(e(this).val().trim());
                    }),
                    h
                      .append(g)
                      .append(
                        m
                          .addClass("new-option")
                          .append(c.clone().attr({ align: "center" }).append(r))
                          .append(c.clone().append(i))
                          .append(c.clone().append(o))
                          .append(
                            c.clone().append(
                              e(
                                '<a href="javascript:;" class="button">+</a>'
                              ).click(function (e) {
                                var t;
                                return (
                                  (t = (function (e, t, n) {
                                    if (!e) return "Value is required";
                                    if (e in a.options)
                                      return "Value should be unique";
                                    return "";
                                  })(i.val(), o.val(), r.get(0).checked))
                                    ? alert(t)
                                    : f(i.val(), o.val(), r.get(0).checked),
                                  !1
                                );
                              })
                            )
                          )
                      )
                      .append(
                        '<tr><td colspan="4"><a href="javascript:;" class="option-editor-import local">' +
                          (0, n.S)("Import From CSV") +
                          '</a> | <a href="javascript:;" class="option-editor-export local">' +
                          (0, n.S)("Export To CSV") +
                          '</a> | <a href="javascript:;" class="option-editor-sort local">' +
                          (0, n.S)("Sort Options") +
                          "</a></td></tr>"
                      ),
                    l.before(h);
                  var v = e("<div></div>").addClass("options-editor");
                  for (var y in (h.wrap(v),
                  l.hide(),
                  l
                    .closest("div")
                    .find(".options-editor table")
                    .sortable({
                      items: "tr.option",
                      stop: function (t, n) {
                        var i = [],
                          o = l
                            .closest("div")
                            .find(".options-editor table")
                            .sortable("toArray");
                        for (var r in o) i.push(e("#" + o[r]).data("key"));
                        !(function (t) {
                          (a.order = t), l.val(e.toJSON(a));
                        })(i);
                      },
                    }),
                  a.order)) {
                    var b = a.order[y];
                    f(b, a.options[b], -1 != e.inArray(b, a.default));
                  }
                  l.val(e.toJSON(a)),
                    l
                      .closest("div")
                      .find(".option-editor-import")
                      .click(function () {
                        var t = e("<div>")
                          .css("display", "none")
                          .html(
                            '<div class="info"><strong>Existing options will be replaced                     with options from this list.</strong><br />One option in each line,                     key and value should be separated by comma, example:                     <br /><pre>key1,Title 1<br />key2,Title 2</pre></div>'
                          );
                        t.append(
                          '<textarea class="am-el-wide" style="margin-bottom:1em" rows="20" name="option-editor-import-csv"></textarea>'
                        );
                        var n = amUrl("/admin-fields/parse-csv", 1);
                        return (
                          t.dialog({
                            modal: !0,
                            title: "Import From CSV",
                            width: 800,
                            position: {
                              my: "center",
                              at: "center",
                              of: window,
                            },
                            buttons: {
                              Ok: function () {
                                e.post(
                                  n[0],
                                  jQuery.merge(n[1], [
                                    {
                                      name: "csv",
                                      value: e(this)
                                        .find(
                                          "textarea[name=option-editor-import-csv]"
                                        )
                                        .val(),
                                    },
                                  ]),
                                  function (n, i, o) {
                                    for (var r in a.options) p(r);
                                    e.each(n, function () {
                                      a.options.hasOwnProperty(this[0]) ||
                                        f(this[0], this[1], this[2]);
                                    }),
                                      t.dialog("close");
                                  }
                                );
                              },
                              Cancel: function () {
                                e(this).dialog("close");
                              },
                            },
                            close: function () {
                              t.empty(), t.remove();
                            },
                          }),
                          !1
                        );
                      }),
                    l
                      .closest("div")
                      .find(".option-editor-export")
                      .click(function () {
                        var t = e("<div>").css("display", "none");
                        t.append(
                          '<textarea class="am-el-wide" style="margin-bottom:1em" rows="20" name="option-editor-export-csv"></textarea>'
                        );
                        var a = amUrl("/admin-fields/get-csv", 1);
                        e.get(
                          a[0],
                          jQuery.merge(a[1], [
                            { name: "data", value: l.val() },
                          ]),
                          function (e, a, n) {
                            return (
                              t.find("[name=option-editor-export-csv]").val(e),
                              t.dialog({
                                modal: !0,
                                title: "Export to CSV",
                                width: 800,
                                position: {
                                  my: "center",
                                  at: "center",
                                  of: window,
                                },
                                close: function () {
                                  t.empty(), t.remove();
                                },
                              }),
                              !1
                            );
                          }
                        );
                      }),
                    l
                      .closest("div")
                      .find(".option-editor-sort")
                      .click(function () {
                        var e = JSON.parse(l.val()),
                          t = [];
                        for (var n in a.options) t.push([n, a.options[n]]);
                        for (var n in (t.sort(function (e, t) {
                          return e[1].localeCompare(t[1]);
                        }),
                        a.options))
                          p(n);
                        return (
                          t.forEach(function (t) {
                            f(t[0], t[1], e.default.includes(t[0]));
                          }),
                          !1
                        );
                      });
                })();
              }
              function u(e) {
                var t,
                  a,
                  n = 0;
                if (0 == (e = e.toString()).length) return n;
                for (t = 0, a = e.length; t < a; t++)
                  (n = (n << 5) - n + e.charCodeAt(t)), (n |= 0);
                return n;
              }
              function p(t) {
                var n,
                  i = e("#option-editor-item-" + u(t));
                delete a.options[t],
                  -1 != (n = e.inArray(t, a.default)) && a.default.splice(n, 1),
                  -1 != (n = e.inArray(t, a.order)) && a.order.splice(n, 1),
                  l.val(e.toJSON(a)),
                  i.remove(),
                  l
                    .closest("div")
                    .find(".options-editor table")
                    .sortable("refresh");
              }
              function f(t, n, d) {
                (a.options[t] = n),
                  d && -1 == e.inArray(t, a.default) && a.default.push(t),
                  -1 == e.inArray(t, a.order) && a.order.push(t),
                  l.val(e.toJSON(a));
                var f = e(
                    '<a href="javascript:;" style="color:#ba2727">&#10005;</a>'
                  ).click(function (t) {
                    return p(e(this).parents("tr").data("key")), !1;
                  }),
                  h = c.clone().append(f).attr({ align: "center" }),
                  m = e('<input type="checkbox" />');
                m.get(0).checked = d;
                var g = e("<div class='options-editor__label'></div>")
                    .text(n)
                    .data("key", t),
                  v = s
                    .clone()
                    .append(c.clone().attr({ align: "center" }).append(m))
                    .append(c.clone().append(t))
                    .append(c.clone().append(g))
                    .append(h)
                    .addClass("option");
                g.liveEdit({}, function (t) {
                  (a.options[e(this).data("key")] = t), l.val(e.toJSON(a));
                });
                var y =
                  "option-editor-item-" +
                  (function (e) {
                    return u(e);
                  })(t);
                v.prop("id", y),
                  m.click(function () {
                    var t = e.inArray(v.data("key"), a.default);
                    this.checked && -1 == t && a.default.push(v.data("key")),
                      this.checked || -1 == t || a.default.splice(t, 1),
                      l.val(e.toJSON(a));
                  }),
                  l.parent().find("tr.new-option").before(v),
                  v.data("key", t),
                  i.val(""),
                  o.val(""),
                  (r.get(0).checked = !1),
                  l
                    .closest("div")
                    .find(".options-editor table")
                    .sortable("refresh");
              }
            });
          };
        })(jQuery);
      },
      65100: function (e, t, a) {
        "use strict";
        a.r(t);
        a(69826),
          a(41539),
          a(68309),
          a(47042),
          a(82526),
          a(41817),
          a(32165),
          a(66992),
          a(78783),
          a(33948);
        function n(e) {
          return (
            (n =
              "function" == typeof Symbol && "symbol" == typeof Symbol.iterator
                ? function (e) {
                    return typeof e;
                  }
                : function (e) {
                    return e &&
                      "function" == typeof Symbol &&
                      e.constructor === Symbol &&
                      e !== Symbol.prototype
                      ? "symbol"
                      : typeof e;
                  }),
            n(e)
          );
        }
        !(function (e) {
          var t = {
            init: function (t) {
              return this.each(function () {
                var t = e(this);
                if (!t.data("player-config")) {
                  t.data("player-config", 1);
                  var a = t.find("input[type=hidden]").val();
                  a &&
                    "preset" != a.substr(0, 6) &&
                    t.find("select option[value=--custom--]").data("config", a);
                  var n = e("<div></div>");
                  e("body").append(n),
                    n.hide(),
                    n.addClass("player-config-container"),
                    t.find(".player-config-edit a").click(function () {
                      n.dialog({
                        modal: !0,
                        title: "Player Config",
                        width: 800,
                        position: { my: "center", at: "center", of: window },
                        buttons: {
                          Cancel: function () {
                            e(this).dialog("close");
                          },
                          Save: function () {
                            n
                              .find("form")
                              .append(
                                e('<input type="hidden" name="_id" />').val(
                                  t.find("select option:selected").val()
                                )
                              ),
                              e.get(
                                amUrl(
                                  "/admin-player-config/update?" +
                                    n.find("form").serialize()
                                ),
                                {},
                                function (e, a, i) {
                                  t
                                    .find("select option:selected")
                                    .data("config", e),
                                    t.find("select").change(),
                                    n.dialog("close");
                                }
                              );
                          },
                        },
                        open: function () {
                          var a = amUrl("/admin-player-config/edit", 1);
                          e.post(
                            a[0],
                            jQuery.merge(a[1], [
                              {
                                name: "config",
                                value: t
                                  .find("select option:selected")
                                  .data("config"),
                              },
                            ]),
                            function (e, t, a) {
                              n.empty().append(e);
                            }
                          );
                        },
                        close: function () {
                          n.empty();
                        },
                      });
                    }),
                    t.find(".player-config-save a").click(function () {
                      n.dialog({
                        modal: !0,
                        title: "Save Presets",
                        width: 450,
                        position: { my: "center", at: "center", of: window },
                        buttons: {
                          Cancel: function () {
                            e(this).dialog("close");
                          },
                          Save: function () {
                            var a = amUrl(
                              "/admin-player-config/preset-save",
                              1
                            );
                            e.post(
                              a[0],
                              jQuery.merge(a[1], [
                                {
                                  name: "config",
                                  value: t
                                    .find("select option[value=--custom--]")
                                    .data("config"),
                                },
                                {
                                  name: "name",
                                  value: n.find("form input[name=name]").val(),
                                },
                              ]),
                              function (a, i, o) {
                                var r = e("<option></option>")
                                  .text(a.name)
                                  .val(a.id)
                                  .data("config", a.config);
                                t
                                  .find("select option[value=--custom--]")
                                  .data("config", null),
                                  t.find("select").append(r).val(a.id).change(),
                                  n.dialog("close");
                              }
                            );
                          },
                        },
                        open: function () {
                          var t = amUrl("/admin-player-config/preset", 1);
                          e.post(t[0], t[1], function (e, t, a) {
                            n.empty().append(e);
                          });
                        },
                        close: function () {
                          n.empty(), n.remove();
                        },
                      });
                    }),
                    t.find(".player-config-delete a").click(function () {
                      if (
                        confirm("Are you realy want to delete this preset?")
                      ) {
                        var a = amUrl("/admin-player-config/preset-delete", 1);
                        e.post(
                          a[0],
                          jQuery.merge(a[1], [
                            {
                              name: "_id",
                              value: t.find("select option:selected").val(),
                            },
                          ]),
                          function (e, a, n) {
                            t
                              .find("select option[value=--custom--]")
                              .data("config", e.config),
                              t.find("select").val("--custom--"),
                              t.find("select").change(),
                              t
                                .find("select option[value=" + e.id + "]")
                                .remove();
                          }
                        );
                      }
                      return !1;
                    }),
                    t
                      .find("select")
                      .change(function () {
                        switch (
                          (t
                            .find(
                              ".player-config-edit,.player-config-save,.player-config-delete"
                            )
                            .hide(),
                          this.value)
                        ) {
                          case "--global--":
                            t.find("input[type=hidden]").val("");
                            break;
                          case "--custom--":
                            t
                              .find("input[type=hidden]")
                              .val(
                                t.find("select option:selected").data("config")
                              ),
                              t
                                .find(".player-config-edit")
                                .css("display", "inline-block"),
                              t.find("select option:selected").data("config") &&
                                t
                                  .find(".player-config-save")
                                  .css("display", "inline-block");
                            break;
                          default:
                            t.find("input[type=hidden]").val(this.value),
                              t
                                .find(".player-config-edit")
                                .css("display", "inline-block"),
                              t
                                .find(".player-config-delete")
                                .css("display", "inline-block");
                        }
                      })
                      .change();
                }
              });
            },
          };
          e.fn.playerConfig = function (a) {
            return t[a]
              ? t[a].apply(this, Array.prototype.slice.call(arguments, 1))
              : "object" !== n(a) && a
              ? void e.error(
                  "Method " + a + " does not exist on jQuery.playerConfig"
                )
              : t.init.apply(this, arguments);
          };
        })(jQuery);
      },
      9730: function (e, t, a) {
        "use strict";
        a.r(t);
        a(74916),
          a(15306),
          a(83753),
          a(4723),
          a(69826),
          a(41539),
          a(47042),
          a(82526),
          a(41817),
          a(32165),
          a(66992),
          a(78783),
          a(33948);
        function n(e) {
          return (
            (n =
              "function" == typeof Symbol && "symbol" == typeof Symbol.iterator
                ? function (e) {
                    return typeof e;
                  }
                : function (e) {
                    return e &&
                      "function" == typeof Symbol &&
                      e.constructor === Symbol &&
                      e !== Symbol.prototype
                      ? "symbol"
                      : typeof e;
                  }),
            n(e)
          );
        }
        !(function (e) {
          var t = {
            init: function (t) {
              return this.each(function () {
                var a = e(this),
                  n = a.data("resourceaccess");
                if (!n || !n.initialized) {
                  a.data("resourceaccess", {
                    options: e.extend(
                      {
                        without_period: !1,
                        with_date_based: !1,
                        with_stop: !1,
                      },
                      t
                    ),
                    initialized: !0,
                  }),
                    e(".free-switch a", a).click(function () {
                      var t = e(this).data("access");
                      switch (
                        (e(".free-switch", a).hide(),
                        e(".free-switch." + t + "-access", a).show(),
                        t)
                      ) {
                        case "protected":
                          a.resourceaccess(
                            "removeItem",
                            "free_without_login",
                            0
                          ),
                            a.resourceaccess("removeItem", "free", 0);
                          break;
                        case "free":
                          a.resourceaccess(
                            "addItem",
                            "free",
                            "Free",
                            0,
                            "",
                            "",
                            ""
                          ),
                            a.resourceaccess(
                              "removeItem",
                              "free_without_login",
                              0
                            );
                          break;
                        case "free_without_login":
                          a.resourceaccess(
                            "addItem",
                            "free_without_login",
                            "Free, login not required",
                            0,
                            "",
                            "",
                            ""
                          ),
                            a.resourceaccess("removeItem", "free", 0);
                      }
                    });
                  var i = JSON.parse(e(".resourceaccess-init", a).val()),
                    o = e(".access-items", a);
                  if (i)
                    for (var r in i) {
                      var l = e("optgroup." + r, o).data("text");
                      for (var s in i[r]) {
                        var c = i[r][s];
                        a.resourceaccess(
                          "addItem",
                          r,
                          l,
                          s,
                          c.text,
                          c.start,
                          c.stop
                        );
                      }
                      if ("free" == r) {
                        e(".protected-access, .free-access", a).toggle();
                        break;
                      }
                      if ("free_without_login" == r) {
                        e(
                          ".protected-access, .free_without_login-access",
                          a
                        ).toggle();
                        break;
                      }
                    }
                  e(".access-items", a).change(function () {
                    if (!(this.selectedIndex <= 0)) {
                      var t = this.options[this.selectedIndex],
                        n = e(t).closest("optgroup");
                      a.resourceaccess(
                        "addItem",
                        n.attr("class"),
                        n.data("text"),
                        t.value.replace(/^[_a-z]*/, ""),
                        t.text,
                        null,
                        null
                      );
                    }
                  });
                }
              });
            },
            removeItem: function (t, a) {
              var n = this.attr("id") + "[" + t + "][" + a + "]";
              e("input[type='hidden'][name='" + n + "']", this)
                .closest(".item")
                .remove();
            },
            addItem: function (a, n, i, o, r, l) {
              var s = e(this),
                c = e("<div class='item'>");
              c.html(" <i>" + n + "</i> <b>" + o + "</b>");
              var d = e(
                "<a href='javascript:;' class='am-link-del'>&#10005;</a>"
              );
              d.click(function () {
                c.remove(),
                  e(
                    ".access-items optgroup." +
                      a +
                      " option[value='" +
                      a +
                      i +
                      "']",
                    s
                  ).prop("disabled", !1);
              }),
                c.prepend(d),
                e(
                  ".access-items optgroup." +
                    a +
                    " option[value='" +
                    a +
                    i +
                    "']",
                  s
                ).prop("disabled", !0);
              var u = this.attr("id"),
                p = e(
                  "<input type='hidden' name='" +
                    u +
                    "[" +
                    a +
                    "][" +
                    i +
                    "]' />"
                );
              if (
                (p.val(e.toJSON({ start: r, stop: l, text: o })),
                c.append(p),
                !a.match(/^(free|special)/) &&
                  "user_group_id" != a &&
                  !this.data("resourceaccess").options.without_period)
              ) {
                var f = e(
                  "<a class='am-resource-access-start local' href='javascript:;'></a>"
                );
                f.text(e(this).resourceaccess("getLinkText", r, !0)).click(
                  function (n) {
                    n.stopPropagation(), t.openEditor(e(this), !0, c, a, !0);
                  }
                );
                var h = e("<a href='javascript:;' class='local'></a>");
                h.text(e(this).resourceaccess("getLinkText", l, !1)).click(
                  function (n) {
                    n.stopPropagation(), t.openEditor(e(this), !1, c, a, !0);
                  }
                );
                var m = jQuery(
                    "<span class='am-resource-access-stop-text'></span>"
                  ),
                  g = jQuery(
                    "<span class='am-resource-access-stop-text'></span>"
                  );
                m.append("from "),
                  g.append(" to ").append(h),
                  c.append("&nbsp;").append(m).append(f).append(g),
                  "a" == r && (m.hide(), g.hide());
              }
              if (
                !a.match(/^(free|special)/) &&
                "user_group_id" != a &&
                this.data("resourceaccess").options.without_period &&
                this.data("resourceaccess").options.with_stop
              ) {
                var v = e("<a href='javascript:;' class='local'></a>");
                v.text(e(this).resourceaccess("getLinkText", l, !1)).click(
                  function (n) {
                    n.stopPropagation(), t.openEditor(e(this), !1, c, a, !1);
                  }
                );
                var y = jQuery(
                    "<span class='am-resource-access-stop-text'></span>"
                  ),
                  b = jQuery(
                    "<span class='am-resource-access-stop-text'></span>"
                  );
                y.append("from start"),
                  b.append(" to ").append(v),
                  c.append("&nbsp;").append(y).append(b);
              }
              return e("." + a + "-list", this).append(c), c;
            },
            getLinkText: function (e, t) {
              switch (!0) {
                case "a" == e:
                  return "[access by publish date]";
                case "-1d" == e:
                  return "forever";
                case 0 == e:
                case "" == e:
                case null == e:
                  return t ? "start" : "expiration";
                default:
                  return e;
              }
            },
            openEditor: function (t, a, n, i, o) {
              var r = e(n).closest(".resourceaccess"),
                l = e(
                  "<input type='text' id='resourceaccess-count' size=3 maxlength=5 style='margin-right:.2em'>"
                ),
                s = e("<select id='resourceaccess-unit' size=1></select>");
              s.append(new Option(a ? "start" : "expiration", "")),
                o && s.append(new Option("-th day", "d")),
                a &&
                  "product_id" == i &&
                  (s.append(new Option("-nd payment", "p")),
                  r.data("resourceaccess").options.with_date_based &&
                    s.append(new Option("[access by publish date]", "a"))),
                a || s.append(new Option("forever", "-1d"));
              var c = e(
                "<span class='resourceaccess-edit' style='font-size: 8pt;' />"
              );
              c.append(l), c.append(s);
              var d = n.find("input[type='hidden']");
              c.on("outerClick", function () {
                var i = e.evalJSON(d.val()),
                  o = null,
                  r = s.val();
                (o =
                  "d" != r && "p" != r
                    ? s[0].options[s[0].selectedIndex].text
                    : (r = parseInt(l.val()) + r)),
                  a
                    ? ((i.start = r),
                      jQuery(".am-resource-access-stop-text", n).toggle(
                        "a" != r
                      ))
                    : (i.stop = r),
                  d.val(e.toJSON(i)),
                  t.text(o).show(),
                  c.remove();
              });
              var u = e.evalJSON(d.val()),
                p = a ? u.start : u.stop;
              switch (!0) {
                case p && null != p.match(/^[0-9]+p$/):
                  l.val(parseInt(p)), s.val("p");
                  break;
                case p && null != p.match(/^[0-9]+d$/):
                  l.val(parseInt(p)), s.val("d");
                  break;
                default:
                  s.val(p), l.hide();
              }
              s.change(function () {
                l.toggle("d" == e(this).val() || "p" == e(this).val());
              }),
                t.hide().after(c);
            },
          };
          e.fn.resourceaccess = function (a) {
            return t[a]
              ? t[a].apply(this, Array.prototype.slice.call(arguments, 1))
              : "object" !== n(a) && a
              ? void e.error(
                  "Method " + a + " does not exist on jQuery.resourceaccess"
                )
              : t.init.apply(this, arguments);
          };
        })(jQuery);
      },
      38965: function (e, t, a) {
        "use strict";
        a.r(t),
          a.d(t, {
            reloadAmVars: function () {
              return s;
            },
            amVar: function () {
              return c;
            },
            amVars: function () {
              return d;
            },
            scriptReplaced: function () {
              return u;
            },
          });
        a(41539),
          a(54747),
          a(19601),
          a(82526),
          a(41817),
          a(32165),
          a(66992),
          a(78783),
          a(33948),
          a(91038),
          a(47042),
          a(68309),
          a(74916);
        function n(e) {
          return (
            (function (e) {
              if (Array.isArray(e)) return i(e);
            })(e) ||
            (function (e) {
              if (
                ("undefined" != typeof Symbol && null != e[Symbol.iterator]) ||
                null != e["@@iterator"]
              )
                return Array.from(e);
            })(e) ||
            (function (e, t) {
              if (!e) return;
              if ("string" == typeof e) return i(e, t);
              var a = Object.prototype.toString.call(e).slice(8, -1);
              "Object" === a && e.constructor && (a = e.constructor.name);
              if ("Map" === a || "Set" === a) return Array.from(e);
              if (
                "Arguments" === a ||
                /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a)
              )
                return i(e, t);
            })(e) ||
            (function () {
              throw new TypeError(
                "Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."
              );
            })()
          );
        }
        function i(e, t) {
          (null == t || t > e.length) && (t = e.length);
          for (var a = 0, n = new Array(t); a < t; a++) n[a] = e[a];
          return n;
        }
        var o = {},
          r = 0;
        function l() {
          n(document.getElementsByTagName("script")).forEach(function (e) {
            var t;
            "text/am-vars" == e.type &&
              (o = Object.assign(
                null !== (t = JSON.parse(e.innerText)) && void 0 !== t ? t : {},
                o
              ));
          }),
            r++;
        }
        function s() {
          r = 0;
        }
        function c(e) {
          var t,
            a =
              arguments.length > 1 && void 0 !== arguments[1]
                ? arguments[1]
                : null;
          return r || l(), null !== (t = o[e]) && void 0 !== t ? t : a;
        }
        function d() {
          return r || l(), o;
        }
        function u(e) {
          return c("script-replaced-" + e);
        }
        document.addEventListener("DOMContentLoaded", function (e) {
          s();
        });
      },
      60190: function (e, t, a) {
        "use strict";
        a.d(t, {
          S: function () {
            return i;
          },
        });
        var n = a(38965);
        function i(e) {
          var t;
          return null !== (t = (0, n.amVar)("msg_" + e)) && void 0 !== t
            ? t
            : e;
        }
      },
      3100: function (e, t, a) {
        "use strict";
        function n(e) {
          "loading" != document.readyState
            ? e()
            : document.addEventListener("DOMContentLoaded", e);
        }
        a.d(t, {
          Z: function () {
            return n;
          },
        });
      },
      10049: function (e, t, a) {
        (t = a(23645)(!1)).push([
          e.id,
          ".am-advanced-search-iframe, .am-advanced-search-browse {\n  border: none;\n  width: 100%;\n  height: 100%;\n  position: relative;\n  top: -40px;\n}\n\n.am-advanced-search-browse-div {\n  border: none;\n  width: 100%;\n  height: 100%;\n  position: relative;\n  top: -40px;\n}\n\n.am-advanced-search-browse-back {\n  position: absolute;\n  top: 40px;\n  left: 0;\n  width: 100%;\n  height: 40px;\n  vertical-align: middle;\n  background-color: #f2f3f4;\n}\n\n.am-advanced-search-browse-iframe {\n  position: absolute;\n  top: 80px;\n  left: 0;\n  border: none;\n  width: 100%;\n  height: 100%;\n}",
          "",
        ]),
          (e.exports = t);
      },
      42534: function (e, t, a) {
        (t = a(23645)(!1)).push([
          e.id,
          "body {\n  --blackout-width: 700px;\n  --blackout-close-left: 700px + 3px;\n  --blackout-height: 602px;\n  --blackout-popup-height: 602px + 160px;\n}\n\n.am-body-blackout {\n  position: absolute;\n  z-index: 99000;\n  left: 0;\n  top: 0;\n  width: 100%;\n  height: 100%;\n  background-color: rgba(0, 0, 0, 0.65);\n  display: none;\n}\n.am-body-blackout.is-blacked-out {\n  display: block;\n}\n\n.am-popup-modal {\n  height: var(--blackout-popup-height);\n  width: 100%;\n  position: absolute;\n  left: 0;\n  top: 50px;\n  padding: 0px;\n  opacity: 0;\n  pointer-events: none;\n  transition: all 100ms ease-in-out;\n  z-index: 99090;\n}\n.am-popup-modal .am-popup-dialog {\n  display: block;\n  width: var(--blackout-width);\n  height: var(--blackout-height);\n  margin: 0 auto;\n  background-color: #fff;\n}\n.am-popup-modal.am-is-visible {\n  opacity: 1;\n  pointer-events: auto;\n}\n.am-popup-modal .am-popup-close {\n  float: left;\n  font-size: 1.2rem;\n  cursor: pointer;\n  color: white;\n  position: relative;\n  left: var(--blackout-close-left);\n  top: -25px;\n}\n\n.am-popup-body {\n  /*width: 100%;*/\n  /*height: 100%;*/\n  /*background-color: #2ca02c;;*/\n}",
          "",
        ]),
          (e.exports = t);
      },
      29911: function (e) {
        e.exports =
          '    <span class="am-advanced-search-browse-back">\n        &nbsp;&nbsp;<a href="javascript:" class="local">Back</a>\n    </span>\n    <iframe class="am-advanced-search-browse-iframe" />';
      },
      58399: function (e) {
        e.exports =
          '<div class="am-body-blackout"></div>\n<div class="am-popup-modal">\n    <span class="am-popup-dialog">\n       <i class="am-popup-close">\n           <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="times-circle" role="img" xmlns="http://www.w3.org/2000/svg"\n                viewBox="0 0 512 512" width="28" height="28" alt="am-popup-close"\n                class="svg-inline--fa fa-times-circle fa-w-16 fa-3x">\n               <path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 464c-118.7 0-216-96.1-216-216 0-118.7 96.1-216 216-216 118.7 0 216 96.1 216 216 0 118.7-96.1 216-216 216zm94.8-285.3L281.5 256l69.3 69.3c4.7 4.7 4.7 12.3 0 17l-8.5 8.5c-4.7 4.7-12.3 4.7-17 0L256 281.5l-69.3 69.3c-4.7 4.7-12.3 4.7-17 0l-8.5-8.5c-4.7-4.7-4.7-12.3 0-17l69.3-69.3-69.3-69.3c-4.7-4.7-4.7-12.3 0-17l8.5-8.5c4.7-4.7 12.3-4.7 17 0l69.3 69.3 69.3-69.3c4.7-4.7 12.3-4.7 17 0l8.5 8.5c4.6 4.7 4.6 12.3 0 17z" class=""></path></svg>\n       </i>\n    <span class="am-popup-body"></span>\n    </span>\n</div>\n';
      },
      63505: function () {},
      69625: function (e, t, a) {
        var n = a(93379),
          i = a(10049);
        "string" == typeof (i = i.__esModule ? i.default : i) &&
          (i = [[e.id, i, ""]]);
        var o = { insert: "head", singleton: !1 };
        n(i, o);
        e.exports = i.locals || {};
      },
      86771: function (e, t, a) {
        var n = a(93379),
          i = a(42534);
        "string" == typeof (i = i.__esModule ? i.default : i) &&
          (i = [[e.id, i, ""]]);
        var o = { insert: "head", singleton: !1 };
        n(i, o);
        e.exports = i.locals || {};
      },
      65311: function (e) {
        "use strict";
        e.exports = jQuery;
      },
    },
    a = {};
  function n(e) {
    var i = a[e];
    if (void 0 !== i) return i.exports;
    var o = (a[e] = { id: e, loaded: !1, exports: {} });
    return t[e].call(o.exports, o, o.exports, n), (o.loaded = !0), o.exports;
  }
  (n.m = t),
    (e = []),
    (n.O = function (t, a, i, o) {
      if (!a) {
        var r = 1 / 0;
        for (d = 0; d < e.length; d++) {
          (a = e[d][0]), (i = e[d][1]), (o = e[d][2]);
          for (var l = !0, s = 0; s < a.length; s++)
            (!1 & o || r >= o) &&
            Object.keys(n.O).every(function (e) {
              return n.O[e](a[s]);
            })
              ? a.splice(s--, 1)
              : ((l = !1), o < r && (r = o));
          if (l) {
            e.splice(d--, 1);
            var c = i();
            void 0 !== c && (t = c);
          }
        }
        return t;
      }
      o = o || 0;
      for (var d = e.length; d > 0 && e[d - 1][2] > o; d--) e[d] = e[d - 1];
      e[d] = [a, i, o];
    }),
    (n.n = function (e) {
      var t =
        e && e.__esModule
          ? function () {
              return e.default;
            }
          : function () {
              return e;
            };
      return n.d(t, { a: t }), t;
    }),
    (n.d = function (e, t) {
      for (var a in t)
        n.o(t, a) &&
          !n.o(e, a) &&
          Object.defineProperty(e, a, { enumerable: !0, get: t[a] });
    }),
    (n.g = (function () {
      if ("object" == typeof globalThis) return globalThis;
      try {
        return this || new Function("return this")();
      } catch (e) {
        if ("object" == typeof window) return window;
      }
    })()),
    (n.o = function (e, t) {
      return Object.prototype.hasOwnProperty.call(e, t);
    }),
    (n.r = function (e) {
      "undefined" != typeof Symbol &&
        Symbol.toStringTag &&
        Object.defineProperty(e, Symbol.toStringTag, { value: "Module" }),
        Object.defineProperty(e, "__esModule", { value: !0 });
    }),
    (n.nmd = function (e) {
      return (e.paths = []), e.children || (e.children = []), e;
    }),
    (function () {
      var e;
      n.g.importScripts && (e = n.g.location + "");
      var t = n.g.document;
      if (!e && t && (t.currentScript && (e = t.currentScript.src), !e)) {
        var a = t.getElementsByTagName("script");
        a.length && (e = a[a.length - 1].src);
      }
      if (!e)
        throw new Error(
          "Automatic publicPath is not supported in this browser"
        );
      (e = e
        .replace(/#.*$/, "")
        .replace(/\?.*$/, "")
        .replace(/\/[^\/]+$/, "/")),
        (n.p = e + "../../../../../");
    })(),
    (function () {
      var e = { admin: 0 };
      n.O.j = function (t) {
        return 0 === e[t];
      };
      var t = function (t, a) {
          var i,
            o,
            r = a[0],
            l = a[1],
            s = a[2],
            c = 0;
          if (
            r.some(function (t) {
              return 0 !== e[t];
            })
          ) {
            for (i in l) n.o(l, i) && (n.m[i] = l[i]);
            if (s) var d = s(n);
          }
          for (t && t(a); c < r.length; c++)
            (o = r[c]), n.o(e, o) && e[o] && e[o][0](), (e[r[c]] = 0);
          return n.O(d);
        },
        a = (self.webpackChunkwidgets_js = self.webpackChunkwidgets_js || []);
      a.forEach(t.bind(null, 0)), (a.push = t.bind(null, a.push.bind(a)));
    })();
  var i = n.O(void 0, ["vendors-admin-user", "vendors-admin"], function () {
    return n(50573);
  });
  i = n.O(i);
})();
