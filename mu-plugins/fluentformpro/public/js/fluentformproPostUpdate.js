(()=>{function e(e){return function(e){if(Array.isArray(e))return t(e)}(e)||function(e){if("undefined"!=typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)}(e)||function(e,n){if(!e)return;if("string"==typeof e)return t(e,n);var a=Object.prototype.toString.call(e).slice(8,-1);"Object"===a&&e.constructor&&(a=e.constructor.name);if("Map"===a||"Set"===a)return Array.from(e);if("Arguments"===a||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a))return t(e,n)}(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function t(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,a=new Array(t);n<t;n++)a[n]=e[n];return a}!function(t){var n=window.fluentformpro_post_update_vars.post_selector,a=null;function i(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"";t||(t=""),"string"!=typeof t&&(t=t.length>0?t.map((function(e){return e.value?e.value.toString():e.toString()})):t.toString()),e.hasClass("ff_has_multi_select")?e.data("choicesjs")&&(e.data("choicesjs").removeActiveItems(t),e.data("choicesjs").setChoiceByValue(t)):e.val(t)}function r(n){var a,i=arguments.length>1&&void 0!==arguments[1]?arguments[1]:[],r=!1;"string"!=typeof i&&"number"!=typeof i||("1"===i&&(r=!0),i=i.includes(",")?i.split(","):[i]);var o=(i=(a=[]).concat.apply(a,e(i))).map((function(e){return e.value?e.value.toString().trim():e.toString().trim()}));n.each((function(e,n){var a=t(n);-1!==t.inArray(a.val(),o)||"on"===a.val()&&r?(a.closest(".ff-el-form-check").addClass("ff_item_selected"),a.prop("checked",!0)):a.prop("checked",!1)}))}function o(e,n,a){var i=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"img";if(Array.isArray(n)&&n.length>0)t.each(n,(function(t,n){o(e,n,a,i)}));else{var r=c(n),s='<input type="hidden" name="remove-attachment-key-'.concat(a,'[]" value="').concat(r,'">'),f=l(e,n,s,i);r&&e.closest(".ff-el-input--content").append('<input type="hidden" name="existing-attachment-key-'.concat(a,'[]" value="').concat(r,'">')),e.closest(".ff-el-input--content").append(f)}}function s(e,n){var a="Something is wrong when doing ajax request! Please try again";e.responseJSON&&e.responseJSON.data&&e.responseJSON.data.message?a=e.responseJSON.data.message:e.responseJSON&&e.responseJSON.message?a=e.responseJSON.message:e.responseText&&(a=e.responseText);var i=t("<div/>",{class:"error text-danger"});n.closest(".ff-el-group").addClass("ff-el-is-error"),n.closest(".ff-el-input--content").find("div.error").remove(),n.closest(".ff-el-input--content").append(i.text(a))}function l(e,n,a){var i,r=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"img";if(!n||Array.isArray(n)&&!n.length)return"";"image"===(null===(i=n)||void 0===i?void 0:i.type)&&(r="img");var o=t("<div/>",{class:"ff-post-update-thumb-wrapper",css:{position:"relative","margin-bottom":"15px"}}),s=t("<span/>",{text:"X",title:"Remove "+("file"===r?"File":"Image"),"data-attachment-id":c(n),css:{position:"absolute",background:"#f00","border-radius":"50%",color:"#fff",right:"-3px",top:"-3px",width:"15px",height:"15px",display:"flex","align-items":"center","justify-content":"center","font-weight":"700","font-size":"10px",cursor:"pointer","z-index":1},on:{click:function(){t(this).closest(".ff-el-input--content").append(a),o.remove()}}}),l="";if("string"==typeof n&&"file"===r){var f=n.split(/(\\|\/)/g).pop(),d=f.split(".").pop().toLowerCase().trim();if(!["png","jpg","gif","jpeg","webp","bmp"].includes(d)){var p={};p.url=n,p.filename=f,p.filesize=function(e){var t=0,n=new XMLHttpRequest;n.open("HEAD",e,!1),n.send(null),200===n.status&&(t=n.getResponseHeader("content-length"));return parseInt(t)}(n),n=p}}if("file"===r&&"string"!=typeof n)l=function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:140;return t-=91,e.filesize=(null==e?void 0:e.filesize)||(null==e?void 0:e.filesizeInBytes),'\n                    <div class="ff-upload-preview" data-src="">\n                        <div class="ff-upload-thumb">\n                            <div class="ff-upload-preview-img" style="background-image: url(\''.concat(null==e?void 0:e.url,'\');">\n                            \n                            </div>\n                        </div>\n                        <div class="ff-upload-details">\n                            <div class="ff-upload-filename" style="max-width: ').concat(t,'px;">').concat(null!=e&&e.filename?e.filename:null==e?void 0:e.name,'</div>\n                                <div class="ff-upload-progress-inline ff-el-progress">\n                                    <div class="ff-el-progress-bar" style="width: 100%;"></div>\n                                </div>\n                                <div class="ff-upload-filesize ff-inline-block">').concat(null!=e&&e.filesize?(e.filesize/1024).toFixed(2):0,' KB</div>\n                                <div class="ff-upload-error" style="color:red;"></div>\n                            </div>\n                        </div>\n                    </div>\n               ')}(n,parseInt(e.closest(".ff-el-group").width()));else{var u;if("string"!=typeof n)n=null===(u=n)||void 0===u?void 0:u.url;l='<div style="max-width: 200px;"><img class="ff-post-update-thumb" style="width: 100%;" src="'+n+'" ></div>'}return o.append(s).append(l),o}function c(e){return"string"==typeof e?"":(null==e?void 0:e.ID)||(null==e?void 0:e.id)||""}t(document).on("change","#"+n,(function(e){var n=this,c=t(this).val(),f=t(this).closest("form");if(function(e){var t=e.find(".ff-post-update-thumb-wrapper");t.length&&t.remove()}(f),a&&function(e){var t=e.find(".ff-el-repeater.js-repeater table tbody");t.find("tr").remove(),t.append(a)}(f),c){var d=f.attr("data-form_id");jQuery.post(window.fluentFormVars.ajaxUrl,{action:"fluentformpro_get_post_details",post_id:c,form_id:d,fluentformpro_post_update_nonce:window.fluentformpro_post_update_vars.nonce}).then((function(e){e.data.post?(!function(e,n){t.each(e,(function(e,t){if("post_content"===e)window.wpActiveEditor&&tinyMCE.get(wpActiveEditor).setContent(t);else if("thumbnail"===e){var a=n.find("input[name='featured_image']");if(a.length&&t){var i=l(a,t,'<input type="hidden" name="remove_featured_image" value="1">');a.closest(".ff-el-input--content").append(i)}}else"post_excerpt"===e?n.find("textarea[name='"+e+"']").val(t).trigger("change"):n.find("input[name='"+e+"']").val(t).trigger("change")}))}(e.data.post,f),function(e,n){t.each(e,(function(e,t){var a=n.find('[data-name="'+e+'"]');if("select"===a.attr("type"))i(a,t);else if("checkbox"===a.attr("type"))r(a,t);else{var o=t.map((function(e){return e.label})).join(",");a.val(o)}a.change()}))}(e.data.taxonomy,f),["custom_meta","acf_metas","advanced_acf_metas","mb_general_metas","mb_advanced_metas"].forEach((function(n){var s=e.data[n];!function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[],n=arguments.length>1?arguments[1]:void 0;t.each(e,(function(e,t){!function(e,t){var n=t.name;n.includes(".")&&(n=n.split(".").join("[")+"]");var s=t.value,l=e.find('[data-name="'+n+'"]');l.length||(l=e.find('[name="'+n+'"]'));if(l.length){switch(t.type){case"image":case"image_upload":case"single_image":case"gallery":o(l,s,n,"img");break;case"file":case"file_upload":case"file_input":case"file_advanced":o(l,s,n,"file");break;case"select":case"image_select":case"checkbox":case"radio":case"button_group":case"checkbox_list":!function(e,t){var n="select"===e.attr("type")||"select"===e.prop("nodeName").toLowerCase();n?i(e,t):r(e,t)}(l,s);break;case"date_picker":case"date_time_picker":case"time_picker":!function(e,t,n){var a;"date_picker"===n?a="Ymd":"date_time_picker"===n?a="Y-m-d H:i:s":"time_picker"===n&&(a="H:i:s");if(e.length>0&&e[0]._flatpickr){var i=e[0]._flatpickr,r=i.parseDate(t,a);i.setDate(r)}else e.val(t)}(l,s,t.type);break;case"repeater":!function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:[];if(!t||null==t||!t.length)return;var n=e.find("table"),i=n.find("tbody tr"),r=parseInt(n.attr("data-max_repeat"));t.forEach((function(e){var t=n.find("tbody tr").length-1;if(r&&t===r)n.addClass("repeat-maxed");else{var o=i.clone();a||(a=i.clone());var s=Object.values(e);o.find("td").each((function(e,t){var n=jQuery(this).find(".ff-el-form-control:last-child"),a=n.attr("data-mask");a&&n.mask(a);var i="ffrpt-"+(new Date).getTime()+e,r={value:s[e]||"",id:i};n.prop(r)})),o.insertBefore(i)}})),i.remove();var o=n.attr("data-root_name"),s=0;n.find("tbody tr").each((function(e,t){jQuery(this).find(".ff-el-form-control").each((function(t,n){var a=jQuery(n);0===e&&(s=a.attr("tabindex")),a.prop({name:o+"["+e+"][]"}),a.attr("data-name",o+"_"+t+"_"+e),s&&a.attr("tabindex",s)}))})),n.trigger("repeat_change")}(l,s);break;case"wysiwyg":if(l.hasClass("fluentform-post-content")){var c,f=l.attr("id")||"";null===(c=tinyMCE.get(f))||void 0===c||c.setContent(s)}else l.val(s);break;default:"string"==typeof s&&l.val(s)}l.change()}}(n,t)}))}(s||[],f)}))):s(e,t(n))})).fail((function(e){s(e,t(n))})).always((function(){}))}else f.trigger("reset")})),setTimeout((function(){t("#"+n).change()}),500)}(jQuery)})();