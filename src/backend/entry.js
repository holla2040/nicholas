window.onload = function () {
       var preview = document.getElementById("preview");
       var snapshot = document.getElementById("snapshot");

        previewcontext = preview.getContext("2d");
        snapshotcontext = snapshot.getContext("2d");
        video = document.getElementById("video");
        videoObj = { "video": true };
        image_format= "jpeg";
        jpeg_quality= 85;
        errBack = function(error) {
            console.log("Video capture error: ", error.code); 
        };

    // Put video listeners into place
    if(navigator.getUserMedia) { // Standard
        navigator.getUserMedia(videoObj, function(stream) {
            video.src = window.webkitURL.createObjectURL(stream);
            video.play();
        }, errBack);
    } else if(navigator.webkitGetUserMedia) { // WebKit-prefixed
        navigator.webkitGetUserMedia(videoObj, function(stream){
            video.src = window.webkitURL.createObjectURL(stream);
            video.play();
        }, errBack);
    } else if(navigator.mediaDevices.getUserMedia) { // moz-prefixed
        navigator.mozGetUserMedia(videoObj, function(stream){
            video.src = window.URL.createObjectURL(stream);
            video.play();
        }, errBack);
    }

    // Get-Save Snapshot - image 
    document.getElementById("snap").addEventListener("click", function() {
        previewcontext.drawImage(video, 0, 0, 200, 150);
        snapshotcontext.drawImage(video, 0, 0, 1280, 800);
        $("#photo").val(dateFormat(new Date(),"yymmdd-HHMMss")+".jpg");
        $("#location").focus();
        imageSave();
        $("#snap").css('background-color', 'white');
    });

};

$(document).ready(function () {
    $("#v").focus();
});

function search(v) {
    $("#location").focus();
    //console.log(v.length);
    if (v.length == 22) {
        searchDigikey(v);
        $('#distributor').val('Digi-Key');
    } else {
        searchOctopart(v);
        $('#distributor').val('');
    }
}

function searchDigikey(v) {
    // console.log(v);
    var args = {'barcode':v};
    $.getJSON("dkBarcodeToPart.php", args, function(response){
        $('#quantity').val(response.quantity);
        searchOctopart(response.partnumber);
    });
};

// C0805C106K8PACTU
function searchOctopart(mpn) {
    var packaging = ['Cut Tape','Bulk','Tube'];
    var url = "https://octopart.com/api/v3/parts/match";
    url += '?apikey=629371be&include[]=descriptions&include[]=datasheets&include[]=imagesets'
    url += '&callback=?';

    var queries = [
        {'mpn': mpn}
    ];

    var args = {
        queries: JSON.stringify(queries)
    };

    $.getJSON(url, args, function(response){
        $('#data').empty();
        $('#mpn').val("");
        var queries = response['request']['queries'];
        var results = response['results']
        $('#partnumber').val(queries[0].mpn);
        $.each(results, function(i, result) {
            item = result.items[0];
// console.log(item);
            $('#manufacturer').val(item.manufacturer.name);
            $('#octoparturl').val(item.octopart_url);

            $.each(item.descriptions, function(j, description) {
                if (description.attribution.sources[0].name == 'Digi-Key') {
                    desc = description.value;
                    $('#description').val(desc);
                };
                // console.log(description);
            });

            $('#datasheeturl').val(item.datasheets[0].url);
                
            $.each(item.offers, function(j, offer) {
console.log(offer);
console.log(offer.seller.name);
                if (offer.seller.name == 'Digi-Key') {
                    if (packaging.indexOf(offer.packaging)) {
                        $('#distributor').val("Digi-Key");
                        $('#distributorsku').val(offer.sku);
                        $('#distributorurl').val(offer.product_url);
                    }
                }
            });
            if (item.imagesets[0].medium_image.url) {
                $('#image').val(item.imagesets[0].medium_image.url);
            }

        });
    });
};


function imageSave() {
    var dataUrl = snapshot.toDataURL("image/jpeg", 0.85);
    $("#uploading").show();
    $.ajax({
      type: "POST",
      url: "imagesave.php",
      data: { 
         imgBase64: dataUrl,
         user: "nicholas",        
         photo: $("#photo").val(),        
         userid: 25          
      }
    }).done(function(msg) {
        console.log("saved "+$("#photo").val());
    });
};

function validateForm() {
    var e = 0;
    console.log($("#distributorsku").val());
    if ($("#photo").val().length == 0 ) {
        $("#snap").css('background-color', 'red');
        //alert("please take a photo");
        e += 1;
    }

    if ($("#quantity").val().length == 0 ) {
        $("#quantity").css('background-color', 'red');
        e += 1;
    }

    if ($("#partnumber").val().length == 0 ) {
        $("#partnumber").css('background-color', 'red');
        e += 1;
    }

    if ($("#location").val().length == 0 ) {
        $("#location").css('background-color', 'red');
        e += 1;
    }

    if (e > 0) {
        return false;
    }

    // previewcontext.clearRect(0, 0, 200, 150);
    // snapshotcontext.clearRect(0, 0, 1280, 800);
    return true;
};
