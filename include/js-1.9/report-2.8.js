function toggleAll() {
    if ($expandableRow = $(".expandable-report-row"),
    !($expandableRow.length <= 0)) {
        var e = !1;
        $(".detail-row:visible").each(function(t) {
            e = !0
        }),
        e ? $(".detail-row").hide("fast") : $(".detail-row").toggle("fast")
    }
}
function setFixedColumn() {
    if (FIXED_COLUMN) {
        var e = document.querySelector(".report-container")
          , t = (document.querySelector(".main-table"),
        [].concat.apply([], document.querySelectorAll("tbody th")))
          , a = [].concat.apply([], document.querySelectorAll("thead th"))
          , r = [].concat.apply([], document.querySelectorAll(".dummy-td"));
        document.createElement("div"),
        window.getComputedStyle(a[0]);
        e.addEventListener("scroll", function(o) {
            var n = e.scrollLeft
              , i = e.scrollTop
              , l = new Array;
            t.forEach(function(e, t) {
                e.style.transform = translate(n, 0)
            }),
            a.forEach(function(e, t) {
                e.style.transform = t < 2 ? translate(n, i) : translate(0, i),
                l[t] = e.offsetHeight
            }),
            r.forEach(function(e, t) {
                e.style.transform = translate(n, i),
                e.style.height = l[t] + "px"
            }),
            n > 5 ? $(".freeze-pane-border").addClass("freeze-pane-border-active") : $(".freeze-pane-border").removeClass("freeze-pane-border-active")
        }),
        e.dispatchEvent(new Event("scroll"))
    }
}
function translate(e, t) {
    return "translate(" + e + "px, " + t + "px)"
}
function updateData() {
    $("[name=btnSubmit]").prop("disabled", !0),
    $(".main-table tbody").html(""),
    $(".loading-icon").css("display", "inline-block"),
    $(".report-container").scrollTop(0),
    $.ajax({
        type: "post",
        dataType: "json",
        data: $("#filterForm").serialize(),
        success: function(e) {
            console.log(e)
            e.fileData && $("[name=hidFileData]").val(e.fileData),
            $(".loading-icon").hide(),
            e.header && $(".report-content .table-header-container").html(e.header),
            $(".main-table tbody").append(e.content),
            $(".main-table > tbody").append('<div style="clear:both; height: 2em;"></div>');
            var t = "";
            for (i = 0; i < e.filterInformation.length; i++)
                t += '<div class="div-table-row"><div class="div-table-col">' + e.filterInformation[i].label + '</div><div style="padding:0 0.5em;">:</div><div class="div-table-col">' + e.filterInformation[i].filter + "</div></div>";
            "" != t && (t = '<div class="div-table">' + t + "</div>"),
            $(".filter-information").html(t),
            e.footnote && $(".filter-information").append(e.footnote),
            0 == t.length ? $(".show-filter-information").hide() : $(".show-filter-information").attr("display", "inline-block").show(),
            $(".expandable-report-row").bind("click", function(e) {
                $(this).next(".detail-row").toggle("fast")
            }),
            $("[name=btnSubmit]").prop("disabled", !1),
            e.header && $(".sortable").unbind("click").bind("click", sortableHandler),
            setFixedColumn()
        }
    })
}
function clearAutoCompleteInput(e, t, a) {
    $(e).val(""),
    "string" == jQuery.type(t) ? $(e).closest("form").find("[name='" + t + "']").first().val("") : t.val(""),
    void 0 == a && (a = !0),
    a && $(e).closest("form").bootstrapValidator("revalidateField", $(e).attr("name"))
}
function convertDateToStandartFormat(e) {
    var t = e.split(" / ");
    return t[2] + "-" + t[1] + "-" + t[0]
}
function setAutoComplete(e, t) {
    var a = t.objName
      , r = t.objValue
      , o = t.url;
    $("#" + e + " [name=" + a + "]").autocomplete({
        source: o,
        minLength: 1,
        select: function(t, a) {
            $("#" + e + " [name=" + r + "]").val(a.item.pkey)
        },
        search: function(e, t) {},
        change: function(e, t) {}
    }).change(function() {})
}
function updateChkBoxOnClick(e) {
    var t = $(e).prop("checked") ? 1 : 0;
    $(e).val(t),
    $(e).next().val(t)
}
function updateChkBoxOnChange(e) {
    var t = ""
      , a = 0;
    1 == $(e).val() && (t = "checked",
    a = 1),
    $(e).prev().prop("checked", t).change(),
    $(e).prev().val(a)
}
function updateChkPick(e, t) {
    var a = (e = $(e)).closest(".mnv-checkbox-group");
    if (!e.attr("relignore")) {
        var r = a.find("[name='chkPick[]']:enabled");
        r.prev().attr("relignore", !0),
        r.val(e.next().val()).change(),
        r.prev().removeAttr("relignore"),
        t && t()
    }
}
jQuery(document).ready(function() {
    $("body").scrollToTop({
        distance: 200,
        speed: 1e3,
        easing: "linear",
        animation: "fade",
        animationSpeed: 500,
        trigger: null,
        target: null,
        text: '<div class="back-to-top"></div>',
        skin: null,
        throttle: 250,
        namespace: "scrollToTop"
    }),
    $(".input-date").datepicker({
        currentText: "Now",
        dateFormat: "dd / mm / yy",
        changeMonth: !0,
        changeYear: !0
    }),
    $(".input-month").datepicker({
        dateFormat: "MM yy",
        changeMonth: !0,
        changeYear: !0,
        showButtonPanel: !0,
        onClose: function(e, t) {
            if ($("#ui-datepicker-div").html().indexOf("ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all ui-state-hover") > -1) {
                var a = $("#ui-datepicker-div .ui-datepicker-month :selected").val()
                  , r = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker("setDate", new Date(r,a,1)).trigger("change"),
                $(".input-month").focusout()
            }
        },
        beforeShow: function(e, t) {
            if (t.dpDiv.addClass("month-year-datepicker"),
            (datestr = $(this).val()).length > 0) {
                var a = new Date(datestr);
                year = a.getFullYear(),
                month = a.getMonth(),
                $(this).datepicker("option", "defaultDate", new Date(year,month,1)),
                $(this).datepicker("setDate", new Date(year,month,1))
            }
        }
    }),
    $(".input-date").change(function() {
        var e;
        $(this).closest(".mnv-date-range").find(".input-date").each(function() {
            if (e) {
                Date.parse(convertDateToStandartFormat(e.val())) > Date.parse(convertDateToStandartFormat($(this).val())) && $(this).val(e.val())
            }
            e = $(this)
        })
    }),
    $(".input-month").change(function() {
        var e;
        $(this).closest(".mnv-date-range").find(".input-month").each(function() {
            if (e) {
                Date.parse(convertDateToStandartFormat(e.val())) > Date.parse(convertDateToStandartFormat($(this).val())) && $(this).val(e.val())
            }
            e = $(this)
        })
    }),
    $(".report .toogle-criteria").on("click", function() {
        $(".criteria-panel").toggle()
    }),
    $(".report .show-filter-information").on("click", function() {
        $(".filter-information").toggle();
        var e = $(this).text();
        $(this).text($(this).attr("rel")),
        $(this).attr("rel", e)
    }),
    $("#popup-panel .closebutton").on("click", function() {
        hideOverlayScreen()
    }),
    $(".multi-selectbox").searchableOptionList({
        maxHeight: "250px",
        showSelectAll: !0,
        showSelectionBelowList: !0
    }),
    $(".sortable").bind("click", sortableHandler),
    $("#filterForm").submit(function(e) {
        if ($("[name=btnSubmit]").prop("disabled"))
            return !0;
        $(".export-excel, .export-template").find(".download-icon").show(),
        $(".export-excel, .export-template").find(".check-icon").hide(),
        1 == $("[name=hidExportExcel]").val() || (e.preventDefault(),
        $("[name=hidExportExcel]").val(0),
        updateData())
    }),
    $(".print-report").click(function(e) {
        window.print()
    }),
    $(".export-excel, .export-template").click(function(e) {
        $(this).find(".download-icon").hide(),
        $(this).find(".check-icon").show();
        var t = $(this).attr("reltype");
        $("[name=hidExportExcel]").val(t),
        $("[name=btnSubmit]").prop("disabled", !0),
        $("#filterForm").attr("target", "_blank"),
        $("#filterForm").submit(),
        $("[name=btnSubmit]").prop("disabled", !1),
        $("[name=hidExportExcel]").val(0)
    }),
    $(document).keyup(function(e) {
        try {
            switch (e.keyCode || e.which) {
            case 115:
                toggleAll(),
                e.preventDefault()
            }
        } catch (e) {}
    }),
    $(".sortable").attr("reltype", -1),
    $(".sortable").append('<div class="order-type"></div>'),
    $(".report .toogle-criteria").click(),
    1 == autoLoad && updateData()
});
var sortableHandler = function(e) {
    if ($("[name=btnSubmit]").prop("disabled"))
        return !0;
    var t = $(this).attr("reltype")
      , a = $(this).attr("relcol");
    $("#filterForm [name=hidOrderBy]").val(a),
    $("#filterForm [name=hidOrderType]").val(t),
    $(".sortable").removeClass("sortable-active"),
    $(".sortable .order-type").removeClass("arrow-up").removeClass("arrow-down").hide(),
    $(this).addClass("sortable-active"),
    1 == t ? $(this).find(".order-type:first").addClass("arrow-down").show() : $(this).find(".order-type:first").addClass("arrow-up").show(),
    $(this).attr("reltype", -1 * t),
    $("#filterForm").submit()
};
