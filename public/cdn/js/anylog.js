(function($) {
    "use strict"; // Start of use strict
    let a = $("meta[name='data-spm']").attr("content").trim(),
        b = $("body").data("spm").trim(),
        c = 0,
        d = 0;

    //auto send when load pages
    $(function() {
        var spmKey = a + "." + b + "." + c + "." + d + "." + genId(8);
        console.log(spmKey);
    });

    //send event when click without redirect
    $("a,inpu,button").on("click", function(e) {
        var spmKey = $(this).data("spm") + "." + genId(8);
        console.log(spmKey);
        e.preventDefault();
    });

    function genId(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }
})(jQuery); // End of use strict