!function(){"use strict";window;
/*! *****************************************************************************
    Copyright (c) Microsoft Corporation. All rights reserved.
    Licensed under the Apache License, Version 2.0 (the "License"); you may not use
    this file except in compliance with the License. You may obtain a copy of the
    License at http://www.apache.org/licenses/LICENSE-2.0

    THIS CODE IS PROVIDED ON AN *AS IS* BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
    KIND, EITHER EXPRESS OR IMPLIED, INCLUDING WITHOUT LIMITATION ANY IMPLIED
    WARRANTIES OR CONDITIONS OF TITLE, FITNESS FOR A PARTICULAR PURPOSE,
    MERCHANTABLITY OR NON-INFRINGEMENT.

    See the Apache Version 2.0 License for specific language governing permissions
    and limitations under the License.
    ***************************************************************************** */function e(e,t,n,r){return new(n||(n=Promise))((function(o,i){function a(e){try{u(r.next(e))}catch(e){i(e)}}function s(e){try{u(r.throw(e))}catch(e){i(e)}}function u(e){var t;e.done?o(e.value):(t=e.value,t instanceof n?t:new n((function(e){e(t)}))).then(a,s)}u((r=r.apply(e,t||[])).next())}))}function t(e,t){var n,r,o,i,a={label:0,sent:function(){if(1&o[0])throw o[1];return o[1]},trys:[],ops:[]};return i={next:s(0),throw:s(1),return:s(2)},"function"==typeof Symbol&&(i[Symbol.iterator]=function(){return this}),i;function s(i){return function(s){return function(i){if(n)throw new TypeError("Generator is already executing.");for(;a;)try{if(n=1,r&&(o=2&i[0]?r.return:i[0]?r.throw||((o=r.return)&&o.call(r),0):r.next)&&!(o=o.call(r,i[1])).done)return o;switch(r=0,o&&(i=[2&i[0],o.value]),i[0]){case 0:case 1:o=i;break;case 4:return a.label++,{value:i[1],done:!1};case 5:a.label++,r=i[1],i=[0];continue;case 7:i=a.ops.pop(),a.trys.pop();continue;default:if(!(o=a.trys,(o=o.length>0&&o[o.length-1])||6!==i[0]&&2!==i[0])){a=0;continue}if(3===i[0]&&(!o||i[1]>o[0]&&i[1]<o[3])){a.label=i[1];break}if(6===i[0]&&a.label<o[1]){a.label=o[1],o=i;break}if(o&&a.label<o[2]){a.label=o[2],a.ops.push(i);break}o[2]&&a.ops.pop(),a.trys.pop();continue}i=t.call(e,a)}catch(e){i=[6,e],r=0}finally{n=o=0}if(5&i[0])throw i[1];return{value:i[0]?i[1]:void 0,done:!0}}([i,s])}}}var n=function(){function e(e){this._pathInfo=e.getAttribute("data-like"),this._pathConfirm=e.getAttribute("data-like-confirm"),this._tokenKeyUser=e.getAttribute("data-token-key-user"),this._tokenKeyConfirm=e.getAttribute("data-token-key-confirm")}return Object.defineProperty(e.prototype,"tokenKeyConfirm",{get:function(){return this._tokenKeyConfirm},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"tokenKeyUser",{get:function(){return this._tokenKeyUser},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"pathConfirm",{get:function(){return this._pathConfirm},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"pathInfo",{get:function(){return this._pathInfo},enumerable:!0,configurable:!0}),e}(),r=function(){function e(e){this._likes=e.likes,this._newToken=e.newToken,this._pathAdd=e.pathAdd,this._status=e.status}return e.prototype.getEffectiveLikes=function(){return this._likes+(!1===this._status?1:0)},Object.defineProperty(e.prototype,"pathAdd",{get:function(){return this._pathAdd},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"newToken",{get:function(){return this._newToken},enumerable:!0,configurable:!0}),e.prototype.getStatusAsText=function(){return!0===this._status?"confirmed":!1===this._status?"unconfirmed":"no-information"},e.prototype.isLocked=function(){return null!==this._status},e.prototype.fakeUnconfirmed=function(){this._status=!1},e}(),o=function(){function o(e){this._container=e,this._counterElement=e.querySelector("*[data-like-counter]"),this._statusElement=e.querySelector("*[data-like-status]"),this._formElement=e.querySelector("*[data-like-form]")}return o.getFragmentToken=function(e,t){void 0===t&&(t=64);var n=new URLSearchParams(window.location.hash.substr(1)),r=n.get(e);return null===r||r.length!==t?null:(n.delete(e),window.location.hash=n.toString(),r)},o.prototype.initialize=function(){var e=this,t=new n(this._container);this.handleConfirm(t.tokenKeyConfirm,t.pathConfirm),null===o.acquiredUserToken&&(o.acquiredUserToken=o.getFragmentToken(t.tokenKeyUser),null!==o.acquiredUserToken?localStorage.setItem(o.USER_TOKEN_STORAGE_KEY,o.acquiredUserToken):o.acquiredUserToken=localStorage.getItem(o.USER_TOKEN_STORAGE_KEY)),this.fetchData(t.pathInfo).then((function(t){null!==t.newToken&&null===o.acquiredUserToken&&(o.acquiredUserToken=t.newToken,localStorage.setItem(o.USER_TOKEN_STORAGE_KEY,o.acquiredUserToken)),e.updateUI(t),e.handleForm(t.pathAdd,(function(){t.fakeUnconfirmed(),e.updateUI(t)}))}))},o.prototype.handleConfirm=function(n,r){var i=this;if(!o.confirmHandled){var a=o.getFragmentToken(n);null!==a&&(e(i,void 0,void 0,(function(){var e;return t(this,(function(t){switch(t.label){case 0:return(e=new FormData).append("token",a),[4,fetch(r,{method:"post",body:e})];case 1:return t.sent(),[2]}}))})),o.confirmHandled=!0)}},o.prototype.fetchData=function(n){return e(this,void 0,void 0,(function(){var e;return t(this,(function(t){switch(t.label){case 0:return e=n+(null!==o.acquiredUserToken?"?token="+o.acquiredUserToken:""),[4,fetch(e).then((function(e){return e.json()})).then((function(e){return new r(e)}))];case 1:return[2,t.sent()]}}))}))},o.prototype.updateUI=function(e){this._counterElement.innerHTML="<span>"+String(e.getEffectiveLikes())+"</span>",this._container.classList.remove("loading"),e.isLocked()&&(this._container.classList.add("locked"),this._container.classList.remove("hide-form"));var t=function(t){t.classList.remove.apply(t.classList,Array.from(t.classList).filter((function(e){return e.startsWith("status--")}))),t.classList.add("status--"+e.getStatusAsText())};t(this._container),t(this._statusElement)},o.prototype.handleForm=function(e,t){var n=this;this._container.classList.contains("hide-form")&&this._container.addEventListener("click",(function(){n._container.classList.remove("hide-form"),n._container.classList.add("show-form");var e=n._formElement.querySelector("input");return null!==e&&(null!==o.emailAddress&&(e.value=o.emailAddress),e.focus()),!1}),{once:!0}),this._formElement.addEventListener("submit",(function(r){r.preventDefault&&r.preventDefault();var i=new FormData(n._formElement);return i.append("token",o.acquiredUserToken),o.emailAddress=i.get("email"),fetch(e,{method:"post",body:i}).then((function(e){n._container.classList.remove("show-form"),n._container.classList.remove("sent-form"),n._formElement.remove(),204===e.status?t():n._container.classList.add("error")})),!1}))},o.USER_TOKEN_STORAGE_KEY="mvo_like_token",o.confirmHandled=!1,o.acquiredUserToken=null,o.emailAddress=null,o}(),i=function(){function e(e){this._target=e}return e.prototype.initialize=function(){this._target.addEventListener("click",(function(e){return e.preventDefault&&e.preventDefault(),localStorage.removeItem(o.USER_TOKEN_STORAGE_KEY),location.reload(),!1}))},e}();document.addEventListener("DOMContentLoaded",(function(){var e,t;null!==(e=document.querySelectorAll("*[data-like]"))&&e.forEach((function(e){new o(e).initialize()})),null!==(t=document.querySelectorAll("*[data-like-reset]"))&&t.forEach((function(e){new i(e).initialize()}))}))}();
