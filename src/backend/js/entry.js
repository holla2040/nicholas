function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

(function () {
    'use strict';

    var preview = document.getElementById("preview");
    var snapshot = document.getElementById("snapshot");

    var video = document.querySelector('video');

    var previewcontext = preview.getContext("2d");
    var snapshotcontext = snapshot.getContext("2d");
    var errBack = function(error) {
        console.log("Video capture error: ", error.code); 
    };

function getConstraints(videowidth, videoheight) {
    var constraints = {
                  width: { min: videowidth, ideal: videowidth, max: videowidth},    
                  height: { min: videoheight, ideal: videoheight, max: videoheight},
                  frameRate : {min: 5, max: 8 }
                  };
    return constraints;
};

    // use MediaDevices API
    // docs: https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia
    if (navigator.mediaDevices) {
      // access the web cam
      navigator.mediaDevices.getUserMedia({audio:false,video: getConstraints(1600,1200)})
      // permission granted:
        .then(function(stream) {
          video.srcObject = stream;
          // video.addEventListener('click', takeSnapshot);
          video.play();
        })
        // permission denied:
        .catch(function(error) {
          document.body.textContent = 'Could not access the camera. Error: ' + error.name;
        });
    }

    // Get-Save Snapshot - image 
    document.getElementById("snap").addEventListener("click", function() {
        previewcontext.drawImage(video, 0, 0, 800, 600);
        snapshotcontext.drawImage(video, 0, 0, 1600, 1200);
        $("#photo").val(dateFormat(new Date(),"yymmdd-HHMMss")+".jpg");
        $("#quantity").focus();
        // imageSave();
        $("#snap").css('background-color', 'white');
    });

    var loc = getUrlParameter('location');
    if (loc) {
        $('#location').val(loc);
    } 

})();

$(document).ready(function () {
    $("#quantity").focus();
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
// console.log(offer);
// console.log(offer.seller.name);
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
      },
      async: false
    }).done(function(msg) {
        console.log("saved "+$("#photo").val());
    });
};

function validateForm() {
    var e = 0;
    if ($("#photo").val().length == 0 ) {
        $("#snap").css('background-color', 'red');
        //alert("please take a photo");
        e += 1;
    }

    if ($("#quantity").val().length == 0 ) {
        $("#quantity").css('background-color', 'red');
        e += 1;
    }

/*
    if ($("#partnumber").val().length == 0 ) {
        $("#partnumber").css('background-color', 'red');
        e += 1;
    }
*/

    if ($("#location").val().length == 0 ) {
        $("#location").css('background-color', 'red');
        e += 1;
    }

    if (e > 0) {
        return false;
    }

    // previewcontext.clearRect(0, 0, 200, 150);
    // snapshotcontext.clearRect(0, 0, 1280, 800);

    imageSave();

    $('#submitform').attr('action','entry.php?a=test&location='+$('#location').val());
    return true;
};
