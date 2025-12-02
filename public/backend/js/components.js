function formatDecimalsSAP(value) {
    if (value === null || value === "") {
        return "";
    }

    let strVal = String(value).trim();
    let num = parseFloat(strVal);
    if (isNaN(num)) {
        return "";
    }

    let decimals = 0;
    if (strVal.includes(".")) {
        decimals = strVal.split(".")[1].length;
    }

    return num.toLocaleString("id-ID", {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
}

function formatInputDecimals(target) {
    Inputmask({
        alias: "numeric",
        groupSeparator: ".",
        radixPoint: ",",
        autoGroup: true,
        digits: 3,
        digitsOptional: true,
        rightAlign: false,
        removeMaskOnSubmit: true,
    }).mask(target);
}

function formatInputDecimalsWMS(target) {
    Inputmask({
        alias: "numeric",
        groupSeparator: ".",
        radixPoint: ",",
        autoGroup: true,
        digits: 3,
        digitsOptional: true,
        rightAlign: false,
        removeMaskOnSubmit: false,
    }).mask(target);
}

function formatTimestamp(dateString) {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function showLoadingOverlay(message = "Mohon tunggu...") {
    const overlay = document.getElementById("loading-overlay");
    overlay.style.display = "flex";
    overlay.querySelector("p").textContent = message;
    document.body.style.overflow = "hidden";
}

function hideLoadingOverlay() {
    document.getElementById("loading-overlay").style.display = "none";
    document.body.style.overflow = "";
}

function setDefaultSeries(selector, objectCode, prefix = "BKS") {
    const year = new Date().getFullYear().toString().slice(-2);
    const defaultSeriesText = `${prefix}-${year}`;
    console.log("Default Series: ", defaultSeriesText);
    // Cari data series dari server
    $.ajax({
        url: "/purchasing/seriesSearch",
        data: { q: defaultSeriesText, ObjectCode: objectCode },
        dataType: "json",
    }).done(function (data) {
        if (data.results && data.results.length > 0) {
            let found = data.results.find(
                (item) => item.text === defaultSeriesText
            );
            if (found) {
                let option = new Option(found.text, found.id, true, true);
                $(selector).append(option).trigger("change");
            }
        }
    });
}

function setDefaultSeriesSBY(selector, objectCode, prefix = "SBY") {
    const year = new Date().getFullYear().toString().slice(-2);
    const defaultSeriesText = `${prefix}-${year}`;
    console.log("Default Series: ", defaultSeriesText);
    // Cari data series dari server
    $.ajax({
        url: "/purchasing/seriesSearch",
        data: { q: defaultSeriesText, ObjectCode: objectCode },
        dataType: "json",
    }).done(function (data) {
        if (data.results && data.results.length > 0) {
            let found = data.results.find(
                (item) => item.text === defaultSeriesText
            );
            if (found) {
                let option = new Option(found.text, found.id, true, true);
                $(selector).append(option).trigger("change");
            }
        }
    });
}

function setDefaultWarehouse(selector, whsCode) {
    $.ajax({
        url: "/warehouseSearch",
        data: { q: whsCode, limit: 1 },
        dataType: "json",
    }).done(function (data) {
        if (data.results && data.results.length > 0) {
            let found = data.results.find((item) => item.id === whsCode);
            if (found) {
                let option = new Option(found.text, found.id, true, true);
                $(selector).append(option).trigger("change");
            }
        }
    });
}

function setDefaultDistRules(selector, ocr) {
    $.ajax({
        url: "/costCenterSearch",
        data: { q: ocr, limit: 1 },
        dataType: "json",
    }).done(function (data) {
        if (data.results && data.results.length > 0) {
            let found = data.results.find((item) => item.id === ocr);
            if (found) {
                let option = new Option(found.text, found.id, true, true);
                $(selector).append(option).trigger("change");
            }
        }
    });
}
