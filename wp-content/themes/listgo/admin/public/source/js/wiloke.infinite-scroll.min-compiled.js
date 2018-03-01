!function (a) {
  "use strict";
  a.fn.WilokeInfiniteScroll = function (b) {
    var e,
        g,
        c = { totalAjaxLoaded: 0, direction_enter: "down", direction_entered: "", windowWidth: a(window).outerWidth(), direction_exit: "", direction_exited: "", ajax_action: "wiloke_loadmore_portfolio", appendTo: ".wiloke-items-store", max_posts: 5, totalPostsOfTerm: 0, currentFilterCssClass: ".active", post_type: "", itemCssClass: ".item", navFiltersCssClass: ".wiloke-nav-filter li", navFilterWrapperCssClass: ".wiloke-nav-filter", btnClass: ".wiloke-btn-infinite-scroll", progressingClass: ".wiloke-progress-infinite-scroll", isInfiniteScroll: !0, containerClass: ".wiloke-infinite-scroll-wrapper", additional: {}, is_debug: !1, afterAppended: function () {}, beforeAppend: function (a) {
        return a;
      }, currentTerm: null },
        d = a(this);c = a.extend({}, c, b), "undefined" != typeof oData && (c = a.extend(c, oData));var h = { $el: d, options: c, post__not_in: null, $container: d.closest(c.containerClass), init: function () {
        var a = this;a.events();
      }, events: function () {
        var a = this;a.onLoadmoreClick(), a.onNavFilterClick(), a.options.isInfiniteScroll && a.triggerInfiniteScroll();
      }, getLength: function () {
        var b = this,
            c = a(b.options.navFilterWrapperCssClass, b.$container).find(b.options.currentFilterCssClass).data("filter");return "undefined" == typeof c && (c = a(b.options.navFilterWrapperCssClass, b.$container).find(b.options.currentFilterCssClass).children().data("filter")), "*" != c ? b.$container.find(c).length : b.$container.find(b.options.itemCssClass).length;
      }, onNavFilterClick: function () {
        var b = this;a(b.options.navFiltersCssClass, b.$container).on("click", function (c) {
          c.preventDefault();var d = a(this),
              e = d.data("filter");b.options.currentTerm = d.data("termid"), b.options.totalPostsOfTerm = d.data("total"), "undefined" == typeof e && (e = d.children().data("filter")), d.data("is-loaded") ? (b.$container.find(b.options.progressingClass).removeClass("loading"), a(b.options.btnClass, b.$container).attr("disabled", !0)) : a(b.options.btnClass, b.$container).attr("disabled", !1), a(b.options.appendTo).find(e + b.options.itemCssClass).length < 1 && (a(b.options.btnClass, b.$container).trigger("click"), "yes" == a(b.options.btnClass, b.$container).data("only-one-time") && d.data("is-loaded", !0)), "*" == e ? a(b.options.itemCssClass, b.$container).length == a(b.options.btnClass, b.$container).data("max_posts") ? a(b.options.btnClass, b.$container).remove() : a(b.options.btnClass, b.$container).removeClass("hidden") : a(e, b.$container).length >= b.options.totalPostsOfTerm ? a(b.options.btnClass, b.$container).addClass("hidden") : a(b.options.btnClass, b.$container).removeClass("hidden");
        });
      }, onLoadmoreClick: function () {
        var b = this,
            d = 1;a(b.options.btnClass, b.$container).on("click", function (f) {
          f.preventDefault();var h = a(this),
              i = b.options.currentTerm,
              j = a(this).data("nonce"),
              k = !1;return e && 4 !== e.readyState || h.data("is-ajax") === !0 ? (a(b.options.progressingClass).removeClass("loading"), a(b.options.progressingClass).addClass("loaded"), !1) : (a(b.options.progressingClass).addClass("loading"), a(b.options.progressingClass).removeClass("loaded"), b.options.is_debug || (h.data("is-ajax", !0), h.prop("disabled", !0)), "undefined" == typeof i || null === i ? i = h.data("terms") : k = !0, b.post__not_in = null !== b.post__not_in ? b.post__not_in : h.attr("data-postids"), null != b.post__not_in && "undefined" != typeof b.post__not_in || (b.post__not_in = "", b.$container.find(b.options.itemCssClass).each(function () {
            "undefined" != typeof a(this).data("id") && (b.post__not_in += a(this).data("id") + ",");
          })), void (e = a.ajax({ method: "POST", url: WILOKE_GLOBAL.ajaxurl, cache: !0, data: { action: b.options.ajax_action, totalAjaxLoaded: b.options.totalAjaxLoaded, security: j, term_ids: i, totalPostsOfTerm: b.options.totalPostsOfTerm, post__not_in: b.post__not_in, number_of_loaded: b.getLength(), max_posts: h.data("max_posts"), post_type: b.options.post_type, windowWidth: b.options.windowWidth, additional: b.options.additional }, success: function (e, f, j) {
              var l = j.getResponseHeader("Wiloke-PostsNotIn");if (null !== l && (null === b.post__not_in ? b.post__not_in = l : b.post__not_in = b.post__not_in + "," + l), !e.success) {
                if (a(b.options.progressingClass).toggleClass("loading"), !k) return void h.remove();h.data("is-ajax-" + i, !0), h.prop("disabled", !1);
              }if (e.success && (k ? (a(b.options.navFiltersCssClass, b.$container).find(c.currentFilterCssClass).data("is_loaded", !0), h.data("is-ajax-" + i, !0), a(b.options.navFiltersCssClass, b.$container).find(b.options.currentFilterCssClass).data("is-loaded", !0), a(b.options.progressingClass).addClass("loading")) : b.options.isInfiniteScroll && g.destroy(), h.data("is-ajax", !1), "" == e.data || !e.data)) return h.remove(), void a(b.options.progressingClass).addClass("loaded");d = e.data.next_page;var m = Object.keys(e.data.data.item).length,
                  n = 1,
                  o = "";b.options.totalAjaxLoaded += m, a.each(e.data.data.item, function (c, d) {
                o += d;var f = new Image();f.src = a("img", a(d)).attr("src"), f.onload = function () {
                  if (n == m) {
                    if (o = a(o), o = b.options.beforeAppend(o), a().isotope ? (a(b.options.appendTo, b.$container).append(o).isotope("appended", o), a(b.options.appendTo, b.$container).isotope("reloadItems").isotope({ sortBy: "original-order" })) : (a(b.options.appendTo, b.$container).append(o).masonry("appended", o), a(b.options.appendTo, b.$container).masonry("reloadItems").masonry({ sortBy: "original-order" })), b.options.afterAppended(h), h.data("is-ajax", !1), b.options.isInfiniteScroll === !0 && (g.destroy(), b.triggerInfiniteScroll()), "yes" == e.data.finished) {
                      if (!k) return void a(b.options.progressingClass).fadeOut("slow", function () {
                        a(b.options.progressingClass).remove();
                      });a(b.options.btnClass).addClass("hidden");
                    }h.hasClass("mixed-loadmore-and-infinite-scroll") ? (a(b.options.progressingClass).removeClass("loaded"), a(b.options.progressingClass).addClass("loading")) : a(b.options.progressingClass).toggleClass("loading loaded"), h.prop("disabled", !1);
                  }n++;
                };
              });
            } })));
        });
      }, triggerInfiniteScroll: function () {
        var b = this;g = new Waypoint.Inview({ element: b.$el[0], enter: function (c) {
            b.options.direction_enter == c && a(b.options.btnClass, b.$container).trigger("click");
          }, entered: function (a) {}, exit: function (d) {
            a(c.btnClass, b.$container).trigger("click");
          }, exited: function (a) {
            b.options.direction_exited == a;
          } });
      } };h.init();
  };
}(jQuery);

//# sourceMappingURL=wiloke.infinite-scroll.min-compiled.js.map