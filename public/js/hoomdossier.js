function hoomdossierRound(value, bucket) {
    if (typeof bucket === "undefined") {
        bucket = 5;
    }

    return Math.round(value / bucket) * bucket;
};

function hoomdossierNumberFormat(value, locale, decimals){
    if (typeof value === "string"){
        value = parseFloat(value);
    }
    return value.toLocaleString(locale, { minimumFractionDigits: decimals });
};
