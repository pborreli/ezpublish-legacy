/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.1.0
build: 2026
*/
YUI.add("io-base",function(D){var d="io:start",P="io:complete",B="io:success",F="io:failure",e="io:end",X=0,O={"X-Requested-With":"XMLHttpRequest"},Z={},K=D.config.win;function b(h,q,l){var n,g,p,j,Y,x,k,w=h;q=D.Object(q);g=W(q.xdr||q.form,l);j=q.method?q.method=q.method.toUpperCase():q.method="GET";x=q.sync;k=q.data;if(D.Lang.isObject(q.data)&&D.QueryString){q.data=D.QueryString.stringify(q.data);}if(q.form){if(q.form.upload){return D.io._upload(g,h,q);}else{n=D.io._serialize(q.form,q.data);if(j==="POST"||j==="PUT"){q.data=n;}else{if(j==="GET"){h=R(h,n);}}}}else{if(q.data&&j==="GET"){h=R(h,q.data);}}if(g.t){return D.io.xdr(h,g,q);}if(!x){g.c.onreadystatechange=function(){c(g,q);};}try{g.c.open(j,h,x?false:true);if(q.xdr&&q.xdr.credentials){g.c.withCredentials=true;}}catch(v){if(q.xdr){return A(g,w,q,k);}}if(q.data&&j==="POST"){q.headers=D.merge({"Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"},q.headers);}C(g.c,q.headers);T(g.id,q);try{g.c.send(q.data||"");if(x){p=g.c;Y=q.arguments?{id:g.id,arguments:q.arguments}:{id:g.id};Y=D.mix(Y,p,false,["status","statusText","responseText","responseXML"]);Y.getAllResponseHeaders=function(){return p.getAllResponseHeaders();};Y.getResponseHeader=function(f){return p.getResponseHeader(f);};G(g,q);a(g,q);return Y;}}catch(t){if(q.xdr){return A(g,w,q,k);}}if(q.timeout){S(g,q.timeout);}return{id:g.id,abort:function(){return g.c?N(g,"abort"):false;},isInProgress:function(){return g.c?g.c.readyState!==4&&g.c.readyState!==0:false;}};}function Q(h,i){var g=new D.EventTarget().publish("transaction:"+h),Y=i.arguments,f=i.context||D;Y?g.on(i.on[h],f,Y):g.on(i.on[h],f);return g;}function T(g,f){var Y=f.arguments;f.on=f.on||{};Y?D.fire(d,g,Y):D.fire(d,g);if(f.on.start){Q("start",f).fire(g);}}function G(g,h){var f=g.e?{status:0,statusText:g.e}:g.c,Y=h.arguments;h.on=h.on||{};Y?D.fire(P,g.id,f,Y):D.fire(P,g.id,f);if(h.on.complete){Q("complete",h).fire(g.id,f);}}function U(f,g){var Y=g.arguments;g.on=g.on||{};Y?D.fire(B,f.id,f.c,Y):D.fire(B,f.id,f.c);if(g.on.success){Q("success",g).fire(f.id,f.c);}J(f,g);}function I(g,h){var f=g.e?{status:0,statusText:g.e}:g.c,Y=h.arguments;h.on=h.on||{};Y?D.fire(F,g.id,f,Y):D.fire(F,g.id,f);if(h.on.failure){Q("failure",h).fire(g.id,f);}J(g,h);}function J(f,g){var Y=g.arguments;g.on=g.on||{};Y?D.fire(e,f.id,Y):D.fire(e,f.id);if(g.on.end){Q("end",g).fire(f.id);}H(f);}function N(f,Y){if(f&&f.c){f.e=Y;f.c.abort();}}function A(g,Y,i,f){var h=parseInt(g.id);H(g);i.xdr.use="flash";i.form&&f?i.data=f:i.data=null;return D.io(Y,i,h);}function E(){var Y=X;X++;return Y;}function W(g,Y){var f={};f.id=D.Lang.isNumber(Y)?Y:E();g=g||{};if(!g.use&&!g.upload){f.c=L();}else{if(g.use){if(g.use==="native"){if(K.XDomainRequest){f.c=new XDomainRequest();f.t=g.use;}else{f.c=L();}}else{f.c=D.io._transport[g.use];f.t=g.use;}delete O["X-Requested-With"];}else{f.c={};}}return f;}function L(){return K.XMLHttpRequest?new XMLHttpRequest():new ActiveXObject("Microsoft.XMLHTTP");}function R(Y,f){Y+=((Y.indexOf("?")==-1)?"?":"&")+f;return Y;}function V(Y,f){if(f){O[Y]=f;}else{delete O[Y];}}function C(g,Y){var f;Y=Y||{};for(f in O){if(O.hasOwnProperty(f)){if(Y[f]){break;}else{Y[f]=O[f];}}}for(f in Y){if(Y.hasOwnProperty(f)){g.setRequestHeader(f,Y[f]);}}}function S(f,Y){Z[f.id]=K.setTimeout(function(){N(f,"timeout");},Y);}function M(Y){K.clearTimeout(Z[Y]);delete Z[Y];}function c(Y,f){if(Y.c.readyState===4){if(f.timeout){M(Y.id);}K.setTimeout(function(){G(Y,f);a(Y,f);},0);}}function a(g,h){var Y;try{if(g.c.status&&g.c.status!==0){Y=g.c.status;}else{Y=0;}}catch(f){Y=0;}if(Y>=200&&Y<300||Y===1223){U(g,h);}else{I(g,h);}}function H(Y){if(K&&K.XMLHttpRequest){if(Y.c){Y.c.onreadystatechange=null;}}Y.c=null;Y=null;}b.start=T;b.complete=G;b.success=U;b.failure=I;b.end=J;b._id=E;b._timeout=Z;b.header=V;D.io=b;D.io.http=b;},"3.1.0",{requires:["event-custom-base"],optional:["querystring-stringify-simple"]});