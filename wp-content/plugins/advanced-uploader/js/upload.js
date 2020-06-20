/*
 * upload.js
 *
 * handles large file uploading.
 * version : 4.1
 */
'use strict';

//create global variable
var max_upload = 0;
var max_file_size = 0;
var max_file_size_display = 0;
var adv_max_file_size_display = 0;
var default_action;
var default_url;
var blobSlice;
var requestFileSystem;
var up_plupload;
var msgProgLabel = document.createElement("label");
msgProgLabel.className = "pLabel";
var msgProgress = document.createElement("progress");
var msgProgressDiv = document.createElement("div");
msgProgressDiv.className = "progress";
msgProgressDiv.appendChild(msgProgLabel);
msgProgressDiv.appendChild(msgProgress);

var completeUpload = function (dropzone, file, callback) {
    if(typeof(file.thumbs) !== 'undefined' && file.chunksUploaded) {
    	var fd = new FormData();
    	fd.append('action', 'adv_upload_dropzone');
    	fd.append('security', security.value);
    	fd.append('filename', file.name);
    	fd.append('fileDest', file.dest);
    	fd.append('album', file.album);
    	fd.append('dzuuid', file.upload.uuid);
    	fd.append('dztotalchunkcount', file.upload.totalChunkCount);
    	fd.append('destinations', JSON.stringify(destinations));
    
        if(file.thumbs) {
        	fd.append('meta', JSON.stringify(file.thumbsImageMeta));
        	for (var index=0; index<file.thumbsKeys.length; index++) {
        		var key = file.thumbsKeys[index];
        		var binary = atob(file.thumbsDataURL[key].split(',')[1]);
        		var array = [];
        		for(var i = 0; i < binary.length; i++) {
        			array.push(binary.charCodeAt(i));
        		}
        		
        		//get thumb extension
        		var blob;
        		var thumbExt = file.name.split('.').pop();
        		if( thumbExt.match(/jpg/) )
        			blob = new Blob([new Uint8Array(array)], {type: 'image/jpeg'});
        		else
        			blob = new Blob([new Uint8Array(array)], {type: 'image/png'});
        		fd.append('thumbs[]', blob, file.thumbsImageMeta[key].file);
        	}
        }
    	
    	//update display to show message
	var preview = jQuery(file.previewElement);
    	preview.find('.dz-status-message span').html( 'Completing Upload' );
    	
    	//upload thumbs and add file to WP Libraray
    	jQuery.ajax({	'type': "post",
    			'url': ajaxurl,
    			'data': fd,
    			'enctype': 'multipart/form-data',
    			'encoding': 'multipart/form-data',
    			'cache': false,
    			'processData': false,
    			'contentType': false
    	}).done(function (response) {
    		//check for errors
    		if( response.success === false ) {
            		dropzone.emit("error", file, 'media-upload-error');
    			return;
    		}
    				
    		//update display to show message
		var preview = jQuery(file.previewElement);
	    	preview.find('.dz-status-message span').html( '<a href="'+response.data.editLink+'">Edit</a>' );
    		
    		if( typeof(callback) === 'function' )
    		    callback('success');
		else if( typeof(file.sucessCallback) === 'function' )
			file.sucessCallback('success');
    	}).error(function ( jqXHR, textStatus, errorThrown ) {
            	dropzone.emit("error", file, textStatus);
    	});
    } else if( typeof(callback) === 'function' )
	file.sucessCallback = callback;
};

var createThumbImage = function (dropzone, file, name, callback) {
    var fileReader = new FileReader();
    
    fileReader.onload = function () {
    	//get file extension
    	var ext = name.split('.').pop();
    	
    	var tempImg = new Image();
    	tempImg.src = fileReader.result;
    	tempImg.onload = function() {
    	    var tempW = tempImg.width;
    	    var tempH = tempImg.height;
    	    if (tempH > sizes.thumbnail.height || tempW > sizes.thumbnail.width) {
    	        var imageMeta = {};
    	        var dataURL = {};
    	        var keys = [];
    	        var imageH = tempH;
    	        var imageW = tempW;
    	        var nameslist = '';
    
    		for (var key in sizes) {
    		    tempH = imageH;
    		    tempW = imageW;
                //get thumbnail size
                var MAX_WIDTH = sizes[key].width;
                var MAX_HEIGHT = sizes[key].height;
    
                if (tempH > MAX_HEIGHT || tempW > MAX_WIDTH) {
    		        if (tempW > imageH) {
    		               tempH *= MAX_WIDTH / tempW;
    		               tempW = MAX_WIDTH;
    		        } else {
    		               tempW *= MAX_HEIGHT / tempH;
    		               tempH = MAX_HEIGHT;
    		        }
    		    
    		        //round down image dimesions
    		        tempW = Math.round(tempW);
    		        tempH = Math.round(tempH);
    		        
    		        //set thumbnail filename
    		        var filename = "-"+tempW+"x"+tempH+".jpg";
    		        
    		        if (nameslist.search(filename) == -1) {
    		        	nameslist += filename + ';';
    			        var canvas = document.createElement('canvas');
    			        canvas.width = tempW;
    			        canvas.height = tempH;
    			        var ctx = canvas.getContext("2d");
    			        ctx.drawImage(this, 0, 0, tempW, tempH);
    	
    			        keys.push(key);
    			        dataURL[key] = canvas.toDataURL("image/jpeg",0.9);
    		        }
    		        imageMeta[key] = {};
    		        imageMeta[key].file = filename;
    		        imageMeta[key].width = tempW;
    		        imageMeta[key].height = tempH;
    		        imageMeta[key]['mime-type'] = "image/jpeg";
    		    }
    		}
    		
    		//store thumbnails in the images
    		file.thumbs = true;
    		file.thumbsDataURL = dataURL;
    		file.thumbsImageMeta = imageMeta;
    		file.thumbsKeys = keys;
    		
    		callback (dropzone, file);
    	} else
    		file.thumbs = false;
    	    callback (dropzone, file);
        };
    };
    
    fileReader.readAsDataURL(file);
};

      
var pdf = function (dropzone, file, name, callback, htmlObject) {
    var fileReader = new FileReader();
	var pdfDoc = null;
	
	//
	// create thumbnail images
	//
	function createPDFthumb (page, nameslist, imageSizes, keys, imageMeta, dataURL) {
		var key = imageSizes.pop();
		var origViewport = page.getViewport({scale: 1.0});
		var wScale = sizes[key].width / origViewport.width;
		var hScale = sizes[key].height / origViewport.height;
		var scale = wScale>hScale?hScale:wScale;
		var viewport = page.getViewport({scale: scale});
		var canvas = document.createElement('canvas');
		var ctx = canvas.getContext('2d');
		canvas.height = viewport.height;
		canvas.width = viewport.width;
		
	    //set thumbnail filename
	    var filename = "-"+canvas.width+"x"+canvas.height+".png";

		imageMeta[key] = {};
		imageMeta[key].file = filename;
		imageMeta[key].width = canvas.width;
		imageMeta[key].height = canvas.height;
		imageMeta[key]['mime-type'] = "image/png";
	        
	    if (nameslist.search(filename) == -1) {
	       	nameslist += filename + ';';
	       	
			// Render PDF page into canvas context
			var renderContext = {
				canvasContext: ctx,
				viewport: viewport
			};
	
            var renderTask = page.render(renderContext);
            renderTask.promise.then(function () {
				keys.push(key);
			    dataURL[key] = canvas.toDataURL("image/png");
				if (imageSizes.length > 0)
					createPDFthumb (page, nameslist, imageSizes, keys, imageMeta, dataURL);
				else {
            		//store thumbnails in the images
            		file.thumbs = true;
            		file.thumbsDataURL = dataURL;
            		file.thumbsImageMeta = imageMeta;
            		file.thumbsKeys = keys;
            		
            		htmlObject.css('background', 'url('+dataURL['thumbnail']+')');
            		callback (dropzone, file);
				}
			});
		} else {
			if (imageSizes.length > 0)
				createPDFthumb (page, nameslist, imageSizes, keys, imageMeta, dataURL);
			else {
        		//store thumbnails in the images
        		file.thumbs = true;
        		file.thumbsDataURL = dataURL;
        		file.thumbsImageMeta = imageMeta;
        		file.thumbsKeys = keys;
        		
        		alert(htmlObject);
        		callback (dropzone, file);
			}
		}
	}
	
    fileReader.onload = function () {
        // Using DocumentInitParameters object to load binary data.
        var loadingTask = pdfjsLib.getDocument({data: this.result});
        loadingTask.promise.then(function(pdf) {
            // Fetch the first page
            pdf.getPage(1).then(function(page) {
			    var keys = [];
			    for (var key in sizes)
				    keys.push(key);
				
			    createPDFthumb (page, '', keys, [], {}, {});
		    });
        });
    };
 
	fileReader.readAsArrayBuffer(file);		
};

var destination = {
    files: [],
    options: '',
    group: '',
    catOpt: '',
    dropzone: null,
    callback: null,
    destSelected: false,
    dlg: jQuery("<div id='dest-popup' />"),
    select: function (file, drop) {
        var i;
        this.dropzone = drop;
        this.files.push(file);

    	//update display to show message
	var preview = jQuery(file.previewElement);
	preview.find('.dz-status-message span').html( 'Select Destination' );

	//destinations options
	if(this.options === '') {
    	this.options = '<option value="">Select Destination</option>';
    	this.group = "";
    	for (i=0; i<destinations.length; i++) {
    		if (this.group != destinations[i][2]) {
    			if (this.group !== "") {
    				this.options += '</optgroup>';
    			}
    			this.options += '<optgroup label="' + destinations[i][2] + '">';
    		}
    		this.options += '<option value="' + i + '">' + destinations[i][0] + '</option>';
    		this.group = destinations[i][2];
    	}

    	if (this.group !== "") {
    		this.options += '</optgroup>';
    	}
	}

    //category options
	if(this.catOpt === '') {
    	this.catOpt = '<option value="">Select category</option>';
    	for (i=0; i<categories.length; i++) {
    		this.catOpt += '<option class="' + categories[i][2] + '" value="' + i + '">' + categories[i][1] + '</option>';
    	}
	}

	//content for popup
	var content;
	if(this.files.length === 1) {
		content = '<p id="eachSelect">Select destination for each file</p>';
        this.dlg.html(content);
	} else if (this.files.length === 2) {
	    content   = '<p>Select destination for all files';
    	content  += '<select id="dest">';
    	content  += this.options;
    	content  += '</select>';
    	content  += '<span id="wg_" class="hide gallery_name"><input class="alignright" type=text />Gallery name: </span>';
    	content  += '<select id="cat" class="hide">';
    	content  += this.catOpt;
    	content  += '</select>';
    	content  += '</p>';
		content += '<div class="dashed"></div>';
		jQuery(content).insertBefore('#eachSelect');
	}
	
	content  = '<div class="option">' + file.name + '<select id="dest' + this.files.length + '" class="file" name="file' + this.files.length + '">';
	content  += this.options;
	content  += '</select>';
	content  += '<span id="wg_' + this.files.length + '" class="hide gallery_name"><input class="alignright" type=text />Gallery name:</span>';
	content  += '<select id="cat' + this.files.length + '" class="hide">';
	content  += this.catOpt;
	content  += '</select></div>';

    this.dlg.append(content);
    
    if(file.type == 'audio/mp3') {
        //MP3 Tag Reader
        new jsmediatags.Reader(file)
          .setTagsToRead(["album"])
          .read({
            onSuccess: function(tag) {
              file.album = tag.tags.album;
            },
            onError: function(error) {
              console.log(':(', error.type, error.info);
            }
          });
    }

	var select = function(e) {
	    var i=0;
		jQuery('#dest-popup .error').remove();
		var destall = jQuery('#dest :selected').val();
		var dests = jQuery('.file :selected');
		if (destall === undefined && dests === undefined)
		    return false;
		if( e.target.id == "dest" && destall === undefined )
			return false;
		if (destall !== undefined && destall !== "") {
			for (i=0; i < destination.files.length; i++)
				destination.files[i].dest = destall;
			
			if( destinations[destall] && destinations[destall][2] == 'Category' ) {
				if( jQuery( "#adv_cat" ).val() === '' ) {
					jQuery( "<div>").attr({'id':'adv_err_cat','class':'clear alignright media-item error'})
						.html('You must enter an existing category')
						.insertAfter('#ac_cat');
					return false;
				}
				//var catName = jQuery( "#adv_cat" ).val();
				var catIndex = jQuery( "#cat" )[0].selectedIndex - 1;
				var catImage = categories[catIndex][3];
				for (i=0; i < destination.files.length; i++) {
					destination.files[i].album = categories[catIndex][0];
					var type = destination.files[i].type;
					if(catImage != "" && type.split('/')[0] != 'image' && type != 'application/pdf') {
				                var preview = jQuery(destination.files[i].previewElement);
						preview.find('.dz-image img').attr("src",catImage);
					}
				}
			} else if( destinations[destall] 
				&& destinations[destall][2] == 'Wordpress Gallery' 
				&& destinations[destall][3] == 'new' ) {
				if( jQuery( "#wg_ input" ).val() === '' ) {
					jQuery( "<div>").attr({'id':'adv_err_gal','class':'clear alignright media-item error'})
						.html('You must enter a name for the new gallery')
						.insertAfter('#wg_ input');
					return false;
				}
				jQuery.post(ajaxurl,
				{
					'action':'adv_file_upload_new_post',
					'security':security,
					'title':JSON.stringify(new Array(jQuery( "#wg_ input" ).val()))
				}, function( response ) {
					destination.dropzone.processQueue();
					destination.destSelected = true;
					destination.dlg.dialog('close');
				}, "json");
				return false;
			}
			destination.dropzone.processQueue();
			destination.destSelected = true;
			destination.dlg.dialog('close');
		} else if (dests.length) {
			var missingDest = false;
			var missingInfo = false;
			var newGalleries = {};
			for (i=0; i < dests.length; i++) {
				var dest = dests[i].value;

				if (dest.length === 0) {
					jQuery( "<div>").attr({'class':'clear alignright media-item error'})
						.html('You must enter a destination')
						.insertAfter('#dest'+i);
					missingDest = true;
				} else {
					destination.files[i].dest = dest;
					if( destinations[dest] && destinations[dest][2] == 'Category' ) {
						var itemIndex = i+1;
						if( jQuery( "#adv_cat"+itemIndex ).val() === '' ) {
							jQuery( "<div>").attr({'id':'adv_err_cat'+i,'class':'clear alignright media-item error'})
								.html('You must enter an existing category')
								.insertAfter('#ac_cat'+i);
							missingInfo = true;
						}
						var catIndex = jQuery( "#cat"+itemIndex )[0].selectedIndex - 1;
						var catImage = categories[catIndex][3];
						destination.files[i].album = categories[catIndex][0];
						var type = destination.files[i].type;
						if(catImage != "" && type.split('/')[0] != 'image' && type != 'application/pdf') {
				                	var preview = jQuery(destination.files[i].previewElement);
							preview.find('.dz-image img').attr("src",catImage);
						}
					} else if( destinations[dest] 
						&& destinations[dest][2] == 'Wordpress Gallery' 
						&& destinations[dest][3] == 'new' ) {
						if( jQuery( "#wg_"+i+" input" ).val() === '' ) {
							jQuery( "<div>").attr({'id':'adv_err_gal'+i,'class':'clear alignright media-item error'})
								.html('You must enter a name for the new gallery')
								.insertAfter('#wg_'+i+' input');
							missingInfo = true;
						}

						if( typeof(newGalleries[jQuery( "#wg_"+i+" input" ).val()]) === 'undefined')
							newGalleries[jQuery( "#wg_"+i+" input" ).val()] = i.toString();
						else
							newGalleries[jQuery( "#wg_"+i+" input" ).val()] += ',' + i.toString();
					} 
				}
			}
			
			if( missingDest ) {
				jQuery( "<div>").attr({'class':'clear alignright media-item error'})
					.html('You must enter a destination for all files')
					.insertAfter('#dest');
				return false;
			}
			if( missingInfo )
				return false;
				
			if( !jQuery.isEmptyObject( newGalleries ) ) {
				jQuery.post(ajaxurl,
				{
					'action':'adv_file_upload_new_post',
					'security':security,
					'title':JSON.stringify( Object.keys( newGalleries ) )
				}, function( response ) {
					for (var key in response) {
						var ids = newGalleries[response[key].title].split(",");
						for(var i = 0; i < ids.length; i++)
							destination.files[ids[i]].album = response[key].id;
					}
				    destination.dropzone.processQueue();
    	    			    //update display to show message
				    var preview = jQuery(file.previewElement);
			    	    preview.find('.dz-status-message span').html( 'Uploading...' );
					destination.destSelected = true;
					destination.dlg.dialog('close');
				}, "json");
				return false;
			} else {
				destination.dropzone.processQueue();
    	    			//update display to show message
				var preview = jQuery(file.previewElement);
			    	preview.find('.dz-status-message span').html( 'Uploading...' );
				destination.destSelected = true;
				destination.dlg.dialog('close');
			}
		} else {
			jQuery( "<div>").attr({'class':'clear alignright media-item error'})
				.html('You must enter a destination')
				.insertAfter('#dest');
			return false;
		}
	};

	var addBoxCheck = function(e) {
		var id = e.target.id.match(/^dest(.*)/)[1];
		var catId = 'cat' + id;

		if( destinations[e.target.selectedIndex-1] && destinations[e.target.selectedIndex-1][2] == 'Category' ) {
   			jQuery( "#wg_" + id ).hide();
    			jQuery( "#adv_err_gal" + id ).remove();
			if( jQuery( "#ac_" + catId ).length === 0 ) {
    				jQuery( "#" + catId ).combobox({ desc: 'adv_' + catId, id: 'ac_' + catId });
		    		if (id === '')
					id = 1;
				if( destination.files[id-1].hasOwnProperty('album') !== 'undefined' ) {
					jQuery( '#ac_' + catId + ' input' ).autocomplete( "search", destination.files[id-1].album );
				}
    		} else
    			jQuery( "#ac_" + catId ).show();
		} else if( destinations[e.target.selectedIndex-1] 
			&& destinations[e.target.selectedIndex-1][2] == 'Wordpress Gallery' 
			&& destinations[e.target.selectedIndex-1][3] == 'new' ) {
    			jQuery( "#ac_" + catId ).hide();
    			jQuery( "#adv_err_" + catId ).remove();
   			jQuery( "#wg_" + id ).css( "display", "block");
		} else {
   			jQuery( "#wg_" + id ).hide();
    			jQuery( "#ac_" + catId ).hide();
    			jQuery( "#adv_err_" + catId ).remove();
    			jQuery( "#adv_err_gal" + id ).remove();
    			
    		var dests = jQuery('.file :selected');
	  		if (id === '' || dests.length == 1)
				select(e);
    		}
	};
	jQuery(document).on( 'change', '.ui-dialog select', addBoxCheck);

	this.dlg.dialog({
		title    : 'Destination',
		dialogClass : 'wp-dialog',
		width    : 'auto',
		modal    : true,
		autoOpen : false,
		closeOnEscape : true,
		buttons : [{
			'text' : 'Cancel',
			'class' : 'button-primary',
			'click' : function() {
				jQuery(this).dialog('close');
				}
		},{
			'text' : 'Select',
			'class' : 'button-primary',
			'click' : select
		}],
		close : function () {
		    if(!destination.destSelected)
		    for (i=0; i < destination.files.length; i++)
			    destination.dropzone.removeFile(destination.files[i]);
	    destination.files = [];
	    destination.destSelected = false;
		jQuery(this).dialog('destroy').remove();
	}});
this.dlg.dialog('open');
}};

jQuery( document ).ready(function( $ ) {
Dropzone.options.dragDropArea = {
    maxFilesize: 1024, //MB
    chunking: true,
    chunkSize: max_upload,
    forceChunking: true,
    previewsContainer: '#media-items',
    previewTemplate: '<div class="dz-preview dz-file-preview"><div class="dz-image"><img data-dz-thumbnail=""></div><div class="dz-details"><div class="dz-filename"><span data-dz-name=""></span></div><div class="dz-size">Size<span data-dz-size=""></span></div><div class="dz-status-message"><span data-dz-status=""></span></div><div class="dz-error-message"><span data-dz-errormessage=""></span></div></div><div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress=""></span></div><div class="dz-success-mark"><svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns"><title>Check</title><defs></defs><g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage"><path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475" fill="#FFFFFF" sketch:type="MSShapeGroup"></path></g></svg></div><div class="dz-error-mark"><svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns"><title>Error</title><defs></defs><g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage"><g id="Check-+-Oval-2" sketch:type="MSLayerGroup" stroke="#747474" stroke-opacity="0.198794158" fill="#FFFFFF" fill-opacity="0.816519475"><path d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" sketch:type="MSShapeGroup"></path></g></g></svg></div></div>',
	    autoProcessQueue: false,
	    accept: function(file, done) {
		//run Dropzones accept functionality first
		done();

		destination.select(file,this);

            //get thumbnail element
	    var preview = jQuery(file.previewElement);
            var thumbnail = preview.find('.dz-image');

			//is image create thumbnail
			if(adv_browser && file.type.startsWith('image')) {
				//update display to show message
				createThumbImage (this, file, file.name, completeUpload);
			//is pdf create thumbnail
			} else if(adv_browser && file.type == 'application/pdf' && typeof(pdfjsLib) != 'undefined') {
				pdf (this, file, file.name, completeUpload, thumbnail);
			} else
				file.thumbs = false;


            switch (file.type.split('/')[0]) {
              case 'image':
                break;
              case 'audio':
                thumbnail.css('background', 'url(../wp-includes/images/media/audio.png) center no-repeat');
                break;
              case 'video':
                thumbnail.css('background', 'url(../wp-includes/images/media/video.png) center no-repeat');
                break;
              case 'text':
                thumbnail.css('background', 'url(../wp-includes/images/media/text.png) center no-repeat');
                break;
              case 'application':
                switch (file.type) {
                  case 'application/pdf':
                    thumbnail.css('background', 'url(../wp-includes/images/media/document.png) center no-repeat');
                    break;
                  case 'application/x-zip-compressed':
                    thumbnail.css('background', 'url(../wp-includes/images/media/archive.png) center no-repeat');
                    break;
                  case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                    thumbnail.css('background', 'url(../wp-includes/images/media/document.png) center no-repeat');
                    break;
                  case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                    thumbnail.css('background', 'url(../wp-includes/images/media/spreadsheet.png) center no-repeat');
                    break;
                  default:
                    thumbnail.css('background', 'url(../wp-includes/images/media/default.png) center no-repeat');
                }
                break;
              default:
                thumbnail.css('background', 'url(../wp-includes/images/media/default.png) center no-repeat');
            }
            thumbnail.css('background-size', 'contain');
	    },
	    chunksUploaded: function(file, done) {
            file.chunksUploaded = true;
            completeUpload(this,file,done);
        },
	};

    $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<div>" )
          .addClass( "custom-combobox" )
          .attr('id', this.options.id)
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .attr('id', this.options.desc)
          .attr('name', this.options.desc)
          .val( value )
          .attr( "title", "" )
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-all" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          });

          
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Show All Items" )
          .height($('#'+this.options.desc).innerHeight()-6)
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false,
          validValue = "";
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            validValue =  $( this ).text();
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          this.input.val( validValue );
          this.element.val( validValue );
          this.input.data( "ui-autocomplete" ).term = validValue;
          return;
        }
 
        // Remove invalid value
        this.input.val( "" );
        this.element.val( "" );
        this.input.data( "ui-autocomplete" ).term = "";
      },

      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  });
