var initLegalText;
!(function(e, t) {
  function n(e, t, n, a, i, s, l, r) {
    var o = {};
    (this.getProductChangeIndex = function() {
      var e = Object.keys(o),
        t = -1;
      return (
        e[e.length - 1] >= a &&
          (t = e.reduce(function(e, t, n) {
            return t < a ? n : e;
          }, -1)),
        t
      );
    }),
      (this.calcMonthlyRates = function() {
        return (o = (function(e, t, n, a, i, s, l) {
          var r = l,
            o = r / 100 + 1,
            d = 1 / 12,
            u = Math.pow(o, d),
            c =
              Math.floor(12 * (Math.pow(1 + r / 100, d) - 1) * 100 * 100) / 100,
            h = 0,
            v = 0,
            p = {};
          if (0 !== e)
            for (var f = t; f <= n; f += a) {
              if (
                (f > s &&
                  ((o = (r = i) / 100 + 1),
                  (u = Math.pow(o, d)),
                  (c =
                    Math.floor(
                      12 * (Math.pow(1 + r / 100, d) - 1) * 100 * 100
                    ) / 100)),
                0 === r)
              )
                (v = Math.floor((e / f) * 100) / 100), (h = e);
              else {
                var m = Math.pow(u, f);
                (v = Math.floor(((e * m) / (m - 1)) * (u - 1) * 100) / 100),
                  (h =
                    (Math.floor(((e * m) / (m - 1)) * (u - 1) * 100) / 100) *
                    f);
              }
              parseFloat(v) >= 9 &&
                (p[f] = {
                  months: f,
                  amount: e,
                  monthlyInstallment: v,
                  interestRate: c,
                  effInterestRate: r,
                  total: h,
                  interestValue: h - e,
                  campaignDuration: s
                });
            }
          return p;
        })(r, e, t, n, i, s, l));
      });
  }
  (t.fn.calculator = function(a) {
    var i = this,
      s = t(e),
      l = a.inputProductPriceFlag,
      r = !1;
    function o(e) {
      var i, o, u, c, h, v, p, f, m, g, b, w, x, z, M;
      function C(s) {
        (a = t.extend(a, s)),
          ((null != (l = a.inputProductPriceFlag) && l) || a.variablePrice) &&
            null != a.$legalTexts &&
            (initLegalText = a.$legalTexts),
          (f = e.find(".duration")).parent().hasClass("duration-slider-wrapper")
            ? ((f = e.find(".duration-slider .duration.duration-less")),
              (m = e.find(".duration-slider .duration.duration-more")))
            : (f
                .wrap('<div class="duration-slider"></div>')
                .wrap('<div class="duration-slider-wrapper"></div>'),
              (m = f.clone(!1)).addClass("duration-more"),
              m.insertAfter(f.addClass("duration-less")));
        var r = e.find(".finance-amount-value input");
        r.off("change.calculator").on("change.calculator", function() {
          var e = t(this),
            n = e.val();
          isNaN(n) && (n = n.replace(/,/, "."));
          var a = parseFloat(n);
          isNaN(a) || isNaN(n) || n < 54
            ? e.addClass("invalid")
            : (e.removeClass("invalid"),
              C({ productPrice: n, variablePrice: !0 }));
        }),
          r.autosize(),
          (i = e.find(".duration .duration-value .prev-month")),
          (u = e.find(".duration .duration-value .next-month")),
          (o = e.find(".duration .duration-value .month")),
          (v = f.find(".duration-value .month")),
          (p = m.find(".duration-value .month")),
          (c = e.find(".more-months-switch")),
          (h = c.find(".button")),
          (b = new n(
            a.minMonth,
            a.maxMonth,
            a.stepMonth,
            a.productChangeMonth,
            a.interestRate,
            a.campaignDuration,
            a.campaignInterestRate,
            a.productPrice
          )),
          (w = b.calcMonthlyRates()),
          (x = Object.keys(w)),
          (z = b.getProductChangeIndex()),
          (M = z > -1 ? Math.min(z, x.length - 1) : x.length - 1),
          e.hasClass("more-month") && (h.removeClass("on"), R(!1)),
          y();
        var d = "click.calculator";
        i.off(d).on(d, function() {
          !t(this).is(":disabled") && M - 1 >= 0 && ((M -= 1), y());
        }),
          u.off(d).on(d, function() {
            !t(this).is(":disabled") && M + 1 < x.length && ((M += 1), y());
          }),
          o.off(d).on(d, function(e) {
            var n = t(this);
            if (!n.hasClass("calculator-duration-selected")) {
              var a = n.html(),
                i = x.indexOf(a);
              i >= 0 && ((M = i), y());
            }
          }),
          h.off(d).on(d, function() {
            var e = t(this);
            e.hasClass("on")
              ? (e.removeClass("on"), R(!1), (M = M > z ? z : M))
              : z > -1 && ((M = z + 1), e.addClass("on"), R(!0)),
              y();
          });
      }
      function y() {
        var n = x[M],
          a = t(".show-on-less-month"),
          r = t(".show-on-more-month");
        if (
          (z > -1 && M > z ? (a.hide(), r.show()) : (a.show(), r.hide()),
          l ||
            (a.find(".campaign-interest-rate").text("0,00 %"),
            a.find(".campaign-duration").text("0")),
          (g = initLegalText),
          w.hasOwnProperty(n))
        ) {
          var o = w[n];
          !(function() {
            i.eq(0).prop("disabled", M <= 0),
              u.eq(0).prop("disabled", M + 1 >= x.length),
              z > -1 &&
                (u.eq(0).prop("disabled", M >= z),
                i.eq(1).prop("disabled", M - 1 <= z),
                u.eq(1).prop("disabled", M + 1 >= x.length));
            var e = [M - 1, M, M + 1],
              t = 1,
              n = e,
              a = t;
            M - 1 < 0 &&
              ((t = 0), (e = 1 === z ? [0, 1] : 0 === z ? [0] : [0, 1, 2]));
            var s = x.length - 1;
            z > -1 && (s = z);
            M >= s &&
              (s >= 2
                ? ((t = 2), (e = [s - 2, s - 1, s]))
                : 1 === s
                ? ((t = 1), (e = [s - 1, s, -1]))
                : ((t = 0), (e = [s, -1, -1])));
            z > -1 &&
              (M - 1 <= z && ((a = 0), (n = [z + 1, z + 2, z + 3])),
              M + 1 >= x.length &&
                (x.length - z - 1 == 1
                  ? ((a = 0), (n = [x.length - 1]))
                  : x.length - z - 1 == 2
                  ? ((a = 1), (n = [x.length - 2, x.length - 1]))
                  : ((a = 2),
                    (n = [x.length - 3, x.length - 2, x.length - 1]))));
            v.removeClass("selected").hide(), p.removeClass("selected").hide();
            for (var l = 0; l < 3; l++) {
              var r = v.eq(l),
                o = e[l];
              void 0 !== x[o]
                ? (r.text(x[o]), t === l && r.addClass("selected"))
                : r.html("&nbsp;"),
                r.show();
              var d = p.eq(l),
                c = n[l];
              void 0 !== x[c]
                ? (d.text(x[c]), a === l && d.addClass("selected"))
                : d.html("&nbsp;"),
                d.show();
            }
          })(),
            z > -1 && (M >= z || e.hasClass("more-month"))
              ? c
                  .slideDown(600)
                  .animate({ opacity: 1 }, { queue: !1, duration: 600 })
              : c
                  .slideUp(600)
                  .animate({ opacity: 0 }, { queue: !1, duration: 600 }),
            (function(t) {
              e
                .find(".finance-amount .finance-amount-value input")
                .val(d(t.amount, 2)),
                e
                  .find(".financial-box .monthly-rate .monthly-rate-value")
                  .text(d(t.monthlyInstallment)),
                e
                  .find(".financial-box .interest-rate .months")
                  .text(d(t.months, 0)),
                e
                  .find(".financial-box .interest-rate .interest-rate-value")
                  .text(d(t.interestRate, 2, "", " %", Math.floor)),
                e
                  .find(".financial-box .interest-value .interest-value-value")
                  .text(d(t.interestValue, 2, "", " €")),
                e
                  .find(
                    ".financial-box .eff-interest-rate .eff-interest-rate-value"
                  )
                  .text(d(t.effInterestRate, 2, "", " %", Math.floor)),
                e
                  .find(".financial-box .amount .amount-value")
                  .text(d(t.amount, 2, "", " €")),
                e
                  .find(".financial-box .total .total-value")
                  .text(d(t.total, 2, "", " €")),
                e.find("input.autosize").autosize();
            })(o),
            (function(e, t, n) {
              g
                .find(".campaign-interest-rate")
                .text(d(e.interestRate, 2, "", " %", Math.floor)),
                g.find(".campaign-duration").text(d(e.months, 0));
            })(
              o,
              w[x[x.length - 1]].interestRate,
              w[x[x.length - 1]].effInterestRate
            ),
            s.trigger("calculator-ui-updated", [e]);
        }
      }
      function R(t) {
        var n = e.find(".duration-slider").width();
        e.find(".duration-slider-wrapper").css("left", (t ? "-=" : "+=") + n),
          t ? e.addClass("more-month") : e.removeClass("more-month");
      }
      e.html(a.template),
        r || ((initLegalText = e.find("ul.legal-text")), (r = !0)),
        null == l || l || (g = e.find("ul.legal-text").empty()),
        (g =
          null === a.$legalTexts
            ? e.find("ul.legal-text")
            : e.find("ul.legal-text").appendTo(t(a.$legalTexts))),
        (e.reload = C),
        (e.updateUI = y),
        (e.generateTableBody = function() {
          var e = [];
          return (
            t.each(w, function(n, a) {
              var i = t("<tr/>");
              i.append(t("<td>" + d(n, 0) + "</td>")),
                i.append(
                  t("<td>" + d(a.monthlyInstallment, 2, "", " €") + "</td>")
                ),
                i.append(
                  t(
                    "<td>" +
                      d(a.interestRate, 2, "", " %", Math.floor) +
                      "</td>"
                  )
                ),
                a.months <= 40
                  ? i.append(t('<td style="background-color:#F6F6F6"></td>'))
                  : i.append(
                      t(
                        "<td>" +
                          d(a.effInterestRate, 2, "", " %", Math.floor) +
                          "</td>"
                      )
                    ),
                i.append(t("<td>" + d(a.total, 2, "", " €") + "</td>")),
                e.push(i);
            }),
            e
          );
        }),
        C();
    }
    function d(e, t, n, a, i) {
      return (
        null == t && (t = 2),
        null == n && (n = ""),
        null == a && (a = ""),
        "" +
          n +
          (e = (e = (function(e, t, n) {
            "function" != typeof n && (n = Math.round);
            var a = Math.pow(10, t);
            return n(e * a) / a;
          })(e, t, i)).toLocaleString("de-DE", {
            useGrouping: !1,
            minimumFractionDigits: t
          })) +
          a
      );
    }
    return (
      (a = t.extend(
        {
          minMonth: 6,
          maxMonth: 72,
          stepMonth: 1,
          productChangeMonth: 41,
          interestRate: 9.9,
          campaignDuration: 30,
          campaignInterestRate: 0,
          productPrice: !1,
          variablePrice: !1,
          $legalTexts: null,
          template:
            '<div class="calculator"> <div class="calculator-wrapper"> <div class="calculator-title"> Ihr möglicher <span class="nowrap">Finanzierungsplan<sup>1</sup></span></div> <div class="finance-amount"> <div class="finance-amount-label">Finanzierungsbetrag</div> <div class="finance-amount-value"><input class="autosize" value="00.00" title="Finanzierungsbetrag"> <span class="unit">&euro;</span></div><div class="ubernehmen-button"><input class="btn btn-change-value submit" type="submit" value="Übernehmen"></div> </div> <div class="duration"> <div class="duration-label"> GEWÜNSCHTE <span class="nowrap">ANZAHL AN RATEN</span></div> <div class="duration-value"> <button class="prev-month"><span class="arrow-icon left"></span></button> <button class="month">1</button> <button class="month selected">2</button> <button class="month">3</button> <button class="next-month"><span class="arrow-icon right"></span></button> </div> </div> <div class="more-months-switch"> <div class="more-month-label"> Zusätzliche Monatsraten anzeigen</div> <div class="more-month-button-wrapper"> <div class="button"> <div class="button-label">Nein</div> <div class="button-label on">Ja</div> </div> </div> </div> <div class="financial-box"> <div class="monthly-rate"> <div class="monthly-rate-label"> Monatliche Rate <span class="nowrap">für Ihren Einkauf<sup class="show-on-less-month">3</sup></span></div> <div class="monthly-rate-value-wrapper"><span class="unit">&euro;</span> <span class="monthly-rate-value">00,00</span> </div> </div> <!-- less months --> <div class="show-on-less-month"> <div class="interest-rate financial-box-detail"> <div class="interest-rate-label detail-label"> Sollzinssatz für diesen Einkauf <span class="nowrap"> für <span class="months">0</span> Monate (jährl., gebunden):<sup>2</sup> </span> </div> <div class="interest-rate-value detail-value nowrap">0,00 %</div> </div> <div class="interest-value financial-box-detail"> <div class="interest-value-label detail-label"> Mögliche Sollzinsen <span class="nowrap"> für diesen Einkauf:<sup>3</sup> </span> </div> <div class="interest-value-value detail-value nowrap">0,00 &euro;</div> </div> </div> <!-- more month --> <div class="show-on-more-month"> <div class="interest-rate financial-box-detail"> <div class="interest-rate-label detail-label"> Sollzinssatz für <span class="months">0</span> Monate (jährl., gebunden): </div> <div class="interest-rate-value detail-value nowrap">0,00 %</div> </div> <div class="eff-interest-rate financial-box-detail"> <div class="detail-label">Effektiver Jahreszinssatz:</div> <div class="eff-interest-rate-value detail-value nowrap">0,00 %</div> </div> <div class="amount financial-box-detail"> <div class="detail-label">Nettodarlehensbetrag:</div> <div class="amount-value detail-value nowrap">0,00 €</div> </div> <div class="interest-value financial-box-detail"> <div class="detail-label">Sollzinsen:</div> <div class="interest-value-value detail-value nowrap">0,00 %</div> </div> <div class="total financial-box-detail"> <div class="detail-label">Gesamtbetrag:</div> <div class="total-value detail-value nowrap">0,00 €</div> </div> </div> </div> </div> <ul class="legal-text"> <li class="legal-1 show-on-more-month"> Finanzierung über einen bonitätsabhängigen Ratenkredit. Kaufpreis entspricht dem Nettodarlehensbetrag; Angaben zugleich repräsentatives Beispiel i. S. d. § 6a Abs. 4 PAngV. </li> <li class="legal-1 show-on-less-month"> Finanzierung über den Kreditrahmen mit Mastercard<sup>®</sup>; Vertragslaufzeit auf unbestimmte Zeit. Nettodarlehensbetrag bonitätsabhängig bis 15.000 &euro;. Näheres zu den Konditionen und Funktionalitäten der Mastercard<sup>®</sup> mit Kreditrahmen entnehmen Sie bitte dem Kreditvertrag. Die Vermittlung durch den Finanzierungspartner erfolgt ausschließlich für den Kreditgeber BNP Paribas S.A. Niederlassung Deutschland, Standort München: Schwanthalerstr. 31, 80336 München. </li> <li class="legal-2 show-on-less-month"> <strong> Gebundener Sollzinssatz von <span class="campaign-interest-rate">0,00 %</span> gilt nur für diesen Einkauf für die ersten <span class="campaign-duration">0</span> Monate ab Vertragsschluss. Danach und für alle weiteren Verfügungen beträgt der veränderliche Sollzinssatz (jährlich) <span class="interest-rate">14,84 %</span> (<span class="eff-interest-rate">15,90 %</span> effektiver Jahreszins) </strong>. Angaben zugleich repräsentatives Beispiel gem. § 6a Abs. 4 PAngV. <strong><u>Bitte beachten Sie:</u></strong> Zahlungen werden erst auf verzinste Verfügungen angerechnet, bei unterschiedlichen Zinssätzen zuerst auf die höher verzinsten; eine Tilgung sollzinsfreier Umsätze findet statt, wenn keine verzinsten Verfügungen mehr vorhanden sind. </li> <li class="legal-3 show-on-less-month"> <strong>Gilt nur, wenn Sie keine weiteren Verfügungen mit dem Kreditrahmen vornehmen.</strong> Bitte beachten Sie, dass die Ratenanzahl bzw. die Höhe der monatlichen Rate abhängig ist von der weiteren Inanspruchnahme des Kreditrahmens. Die monatliche Rate beträgt min. 2,5 % der jeweils höchsten, auf volle 100 &euro; gerundeten Inanspruchnahme des Kreditrahmens, mind. 9 &euro; und kann zusätzlich jederzeit in der Consors Finanz Banking-App verändert werden. </li> </ul> </div> '
        },
        a
      )),
      this.each(function() {
        o(i);
      })
    );
  }),
    (t.fn.autosize = function() {
      var e = this;
      return this.each(function() {
        var t;
        (t = e).next().hasClass("autosize-helper")
          ? t.trigger("input.autosize")
          : t.css("width", 90);
      });
    });
})(window, window.jQuery);

jQuery(function($) {
  var productPrice = parseFloat(
    document
      .querySelector(".woocommerce-Price-amount.amount")
      .innerText.replace(/[^\w,]/g, "")
      .replace(",", ".")
  );

  $("#calculator").calculator({
    minMonth: 6,
    maxMonth: 50,
    stepMonth: 6,
    interestRate: 0,
    campaignDuration: 30,
    campaignInterestRate: 0,
    productPrice: productPrice
  });
});
