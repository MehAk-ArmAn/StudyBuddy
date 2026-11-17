import{r as Y}from"./w13-react.js";const Lt=24e5,Le=31.5,J=256,ut=["#5227FF","#FF9FFC","#B497CF"],ft={capsule:0,sphere:1,box:2,tetrahedron:3},dt={conic:0,radial:1,random:2},Ut=`#version 300 es
precision highp float;
layout(location = 0) in vec2 aCorner;
layout(location = 1) in vec4 aParticle;
layout(location = 2) in vec4 aSeed;
uniform vec2 uSize;
uniform float uPixel;
uniform vec2 uCursor;
uniform float uRing;
uniform float uBand;
uniform float uParticle;
uniform float uAmbient;
uniform float uTime;
uniform float uPulse;
uniform float uVariance;
uniform float uDepth;
uniform float uShape;
uniform float uColorMode;
uniform float uStops;
uniform float uHue;
uniform float uReveal;
uniform float uStreak;
uniform float uCharge;
out vec2 vLocal;
out vec2 vBounds;
out float vU;
out float vAlpha;
out float vSoft;

void main() {
  vec2 rel = aParticle.xy - uCursor;
  float dist = length(rel);
  vec2 radial = dist > 1e-3 ? rel / dist : vec2(1.0, 0.0);
  vec2 dir = radial;
  float speed = length(aParticle.zw);
  float streak = clamp(speed / uStreak, 0.0, 2.5);
  if (speed > 1e-3) {
    vec2 heading = aParticle.zw / speed * sign(dot(aParticle.zw, radial) + 1e-4);
    dir = normalize(mix(radial, heading, smoothstep(0.0, 1.0, streak)));
  }
  float ring = exp(-pow((dist - uRing) / uBand, 2.0));
  float z = aSeed.x;
  float depth = 1.0 + z * 0.32 * uDepth;
  float pulse = 1.0 + sin(uTime * uPulse + aSeed.y) * 0.22 * uVariance;
  float grow = uAmbient + (1.0 - uAmbient) * ring;
  float size = uParticle * depth * pulse * (0.85 + 0.3 * aSeed.w) * (1.0 + uCharge * 0.25 * ring);
  vec2 bounds;
  if (uShape < 0.5) {
    float width = size * 0.17 * mix(0.55, 1.0, ring);
    bounds = vec2(max(width, size * 0.5 * grow) * (1.0 + streak * 1.2), width);
  } else if (uShape < 1.5) {
    bounds = vec2(size * 0.22 * mix(0.45, 1.0, grow));
  } else {
    bounds = vec2(size * 0.26 * mix(0.45, 1.0, grow));
  }
  float soft = 1.0 + max(-z, 0.0) * 1.6 * uDepth;
  vec2 local = aCorner * (bounds + soft * 1.5 / uPixel);
  vec2 world = aParticle.xy + dir * local.x + vec2(-dir.y, dir.x) * local.y;
  gl_Position = vec4(world.x / uSize.x * 2.0 - 1.0, 1.0 - world.y / uSize.y * 2.0, 0.0, 1.0);
  vLocal = local * uPixel;
  vBounds = bounds * uPixel;
  vSoft = soft;
  if (uColorMode < 0.5) {
    vU = atan(radial.y, radial.x) / 6.28318530718 + uHue + (aSeed.z - 0.5) * 0.06;
  } else if (uColorMode < 1.5) {
    vU = clamp(dist / (uRing * 2.4), 0.0, 1.0) + (aSeed.z - 0.5) * 0.06;
  } else {
    vU = floor(aSeed.z * uStops) / max(uStops - 1.0, 1.0);
  }
  float fog = 1.0 - max(-z, 0.0) * 0.35 * uDepth;
  vAlpha = mix(uAmbient, 1.0, ring) * fog * uReveal;
}`,Ct=`#version 300 es
precision highp float;
uniform sampler2D uPalette;
uniform float uShape;
uniform float uColorMode;
uniform float uOpacity;
in vec2 vLocal;
in vec2 vBounds;
in float vU;
in float vAlpha;
in float vSoft;
out vec4 fragColor;

void main() {
  vec2 p = vLocal;
  float d;
  if (uShape < 0.5) {
    float r = vBounds.y;
    d = length(vec2(max(abs(p.x) - max(vBounds.x - r, 0.0), 0.0), p.y)) - r;
  } else if (uShape < 1.5) {
    d = length(p) - vBounds.x;
  } else if (uShape < 2.5) {
    float r = vBounds.x * 0.28;
    vec2 q = abs(p) - (vBounds - r);
    d = length(max(q, 0.0)) + min(max(q.x, q.y), 0.0) - r;
  } else {
    float r = vBounds.x * 1.15;
    vec2 q = vec2(r * 0.5774 - p.x, abs(p.y));
    d = max(dot(q, vec2(0.5, 0.8660254)) - r * 0.5, -q.x - r * 0.5) * 1.2;
  }
  float alpha = clamp(0.5 - d / vSoft, 0.0, 1.0) * vAlpha * uOpacity;
  if (alpha < 0.002) discard;
  vec3 color = texture(uPalette, vec2(uColorMode < 0.5 ? fract(vU) : vU, uColorMode < 0.5 ? 0.25 : 0.75)).rgb;
  fragColor = vec4(color * alpha, alpha);
}`,P=(n,r,a)=>Math.min(a,Math.max(r,n)),Dt=n=>{const r=String(n||"").trim(),a=/^#?([0-9a-f]{3}|[0-9a-f]{6})$/i.exec(r);if(a){const y=a[1].length===3?a[1].replace(/./g,h=>h+h):a[1];return[0,2,4].map(h=>parseInt(y.slice(h,h+2),16)/255)}const c=/^rgba?\(\s*([\d.]+)[\s,]+([\d.]+)[\s,]+([\d.]+)/i.exec(r);return c?[c[1],c[2],c[3]].map(y=>P(parseFloat(y)/255,0,1)):null},It=n=>n<=.04045?n/12.92:Math.pow((n+.055)/1.055,2.4),zt=n=>n<=.0031308?n*12.92:1.055*Math.pow(Math.max(n,0),1/2.4)-.055,ht=n=>{const[r,a,c]=n.map(It),y=Math.cbrt(.4122214708*r+.5363325363*a+.0514459929*c),h=Math.cbrt(.2119034982*r+.6806995451*a+.1073969566*c),w=Math.cbrt(.0883024619*r+.2817188376*a+.6299787005*c);return[.2104542553*y+.793617785*h-.0040720468*w,1.9779984951*y-2.428592205*h+.4505937099*w,.0259040371*y+.7827717662*h-.808675766*w]},Ot=([n,r,a])=>{const c=Math.pow(n+.3963377774*r+.2158037573*a,3),y=Math.pow(n-.1055613458*r-.0638541728*a,3),h=Math.pow(n-.0894841775*r-1.291485548*a,3);return[4.0767416621*c-3.3077115913*y+.2309699292*h,-1.2684380046*c+2.6097574011*y-.3413193965*h,-.0041960863*c-.7034186147*y+1.707614701*h].map(w=>P(zt(w),0,1))},Xt=(n,r)=>{const a=[];n.forEach(h=>{const w=Dt(h);if(!w)return;const p=ht(w),M=Math.atan2(p[2],p[1])*180/Math.PI,T=P(1-Math.abs(M-85)/40,0,1),B=.62+.14*T*T*(3-2*T);if(r&&p[0]>B){const F=1+(p[0]-B)*1.6;a.push([B,p[1]*F,p[2]*F])}else a.push(p)}),a.length||a.push(ht(r?[.1,.1,.12]:[1,1,1]));const c=new Uint8Array(J*2*4);return[[...a,a[0]],a.length>1?a:[a[0],a[0]]].forEach((h,w)=>{for(let p=0;p<J;p++){const M=p/(J-(w===0?0:1))*(h.length-1),T=Math.min(Math.floor(M),h.length-2),B=M-T,F=B*B*(3-2*B),_=h[T],z=h[T+1],Ae=Math.hypot(_[1],_[2])*(1-F)+Math.hypot(z[1],z[2])*F,re=_[1]+(z[1]-_[1])*F,ae=_[2]+(z[2]-_[2])*F,ie=Math.hypot(re,ae),se=ie>1e-4?Ae/ie:0,ge=Ot([_[0]+(z[0]-_[0])*F,re*se,ae*se]);for(let N=0;N<3;N++)c[(w*J+p)*4+N]=Math.round(ge[N]*255);c[(w*J+p)*4+3]=255}}),c},Yt=n=>()=>{n|=0,n=n+1831565813|0;let r=Math.imul(n^n>>>15,1|n);return r=r+Math.imul(r^r>>>7,61|r)^r,((r^r>>>14)>>>0)/4294967296},mt=(n,r,a)=>{const c=n.createShader(r);return c?(n.shaderSource(c,a),n.compileShader(c),c):null},Nt=n=>{const r=mt(n,n.VERTEX_SHADER,Ut),a=mt(n,n.FRAGMENT_SHADER,Ct),c=r&&a?n.createProgram():null;return!r||!a||!c?(r&&n.deleteShader(r),a&&n.deleteShader(a),null):(n.attachShader(c,r),n.attachShader(c,a),n.linkProgram(c),{program:c,vertex:r,fragment:a})},pt=(n,{program:r,vertex:a,fragment:c})=>{const y=n.getProgramParameter(r,n.LINK_STATUS);if(!y){let p=!0;for(const M of[a,c])n.getShaderParameter(M,n.COMPILE_STATUS)||(console.error(n.getShaderInfoLog(M)),p=!1);p&&console.error(n.getProgramInfoLog(r))}if(n.deleteShader(a),n.deleteShader(c),!y)return n.deleteProgram(r),null;const h={},w=n.getProgramParameter(r,n.ACTIVE_UNIFORMS);for(let p=0;p<w;p++){const M=n.getActiveUniform(r,p);M&&(h[M.name]=n.getUniformLocation(r,M.name))}return{program:r,uniforms:h}},vt=(n,r,a)=>{if(r&&n.getProgramParameter(a.program,r.COMPLETION_STATUS_KHR)===!1){requestAnimationFrame(()=>vt(n,r,a));return}n.deleteShader(a.vertex),n.deleteShader(a.fragment),n.deleteProgram(a.program)},Vt=({count:n=1500,magnetRadius:r=9,ringRadius:a=7,waveSpeed:c=.4,waveAmplitude:y=1,waveCount:h=3,particleSize:w=.9,lerpSpeed:p=.05,color:M,colors:T=ut,colorMode:B="conic",colorSpeed:F=.05,autoAnimate:_=!0,particleVariance:z=1,rotationSpeed:Ae=0,depthFactor:re=1,pulseSpeed:ae=3,particleShape:ie="capsule",fieldStrength:se=10,ambient:ge=.25,interactive:N=!0,clickBurst:xt=!0,intro:yt=!0,lightMode:Ue=!1,opacity:At=1,maxDevicePixelRatio:gt=2,className:Ce="",style:wt})=>{const De=Y.useRef(null),Ie=Y.useRef(null),Q=Y.useRef(null),[ze,Et]=Y.useState(0),Oe=M?[M]:Array.isArray(T)&&T.length?T:ut,Xe={count:P(Math.round(n),1,2e4),magnet:Math.max(0,r),ring:Math.max(0,a),waveSpeed:c,waveAmplitude:Math.max(0,y),waveCount:Math.max(0,Math.round(h)),size:Math.max(0,w),follow:P(p,.001,1),palette:Oe,paletteKey:`${Oe.join(",")}|${Ue}`,colorMode:dt[B]??dt.conic,colorSpeed:F,autoAnimate:_,variance:Math.max(0,z),rotation:Ae,depth:Math.max(0,re),pulse:ae,shape:ft[ie]??ft.capsule,tightness:Math.max(0,se),ambient:P(ge,0,1),interactive:N,clickBurst:xt,intro:yt,light:Ue,opacity:P(At,0,1),maxRatio:P(gt,.5,3)},q=Y.useRef(Xe);return q.current=Xe,Y.useEffect(()=>{var U;(U=Q.current)==null||U.call(Q)}),Y.useEffect(()=>{const U=De.current,b=Ie.current;if(!U||!b)return;const e=b.getContext("webgl2",{alpha:!0,premultipliedAlpha:!0,antialias:!1,depth:!1,stencil:!1,powerPreference:"high-performance"}),ee=e?e.getExtension("KHR_parallel_shader_compile"):null,V=e?Nt(e):null;let k=e&&V&&!ee?pt(e,V):null;if(!e||!V||!ee&&!k)return;const te=window.matchMedia("(prefers-reduced-motion: reduce)").matches,we=e.createVertexArray();e.bindVertexArray(we);const Ye=e.createBuffer();e.bindBuffer(e.ARRAY_BUFFER,Ye),e.bufferData(e.ARRAY_BUFFER,new Float32Array([-1,-1,1,-1,-1,1,1,1]),e.STATIC_DRAW),e.enableVertexAttribArray(0),e.vertexAttribPointer(0,2,e.FLOAT,!1,0,0);const ce=e.createBuffer();e.bindBuffer(e.ARRAY_BUFFER,ce),e.enableVertexAttribArray(1),e.vertexAttribPointer(1,4,e.FLOAT,!1,0,0),e.vertexAttribDivisor(1,1);const Ee=e.createBuffer();e.bindBuffer(e.ARRAY_BUFFER,Ee),e.enableVertexAttribArray(2),e.vertexAttribPointer(2,4,e.FLOAT,!1,0,0),e.vertexAttribDivisor(2,1);const le=e.createTexture();e.bindTexture(e.TEXTURE_2D,le),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_MIN_FILTER,e.LINEAR),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_MAG_FILTER,e.LINEAR),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_WRAP_S,e.REPEAT),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_WRAP_T,e.CLAMP_TO_EDGE);const E={width:1,height:1,ratio:1},f={x:0,y:0,vx:0,vy:0,tx:0,ty:0,ready:!1},v={x:0,y:0,inside:!1,down:!1,moved:-1/0};let S=null,Ne="",O=0,qe=0,C=0,H=q.current.intro&&!te&&ze===0?0:1/0,D=0,G=0,ue=!0,fe=document.visibilityState!=="hidden",K=!1;const Mt=t=>{t.paletteKey!==Ne&&(Ne=t.paletteKey,e.bindTexture(e.TEXTURE_2D,le),e.texImage2D(e.TEXTURE_2D,0,e.RGBA8,J,2,0,e.RGBA,e.UNSIGNED_BYTE,Xt(t.palette,t.light)))},Rt=(t,o,s,m)=>{const x=Yt(1337),A=.06,l=o*(1+A*2),$=s*(1+A*2),L=Math.max(1,Math.ceil(Math.sqrt(t*l/$))),X=Math.max(1,Math.ceil(t/L)),g=Array.from({length:L*X},(i,R)=>R);for(let i=g.length-1;i>0;i--){const R=Math.floor(x()*(i+1));[g[i],g[R]]=[g[R],g[i]]}const I=Array.from({length:t},()=>x()*2-1).sort((i,R)=>i-R),d={total:t,width:o,height:s,homeX:new Float32Array(t),homeY:new Float32Array(t),band:new Float32Array(t),phase:new Float32Array(t),agility:new Float32Array(t),delay:new Float32Array(t),orbit:new Float32Array(t),x:new Float32Array(t),y:new Float32Array(t),vx:new Float32Array(t),vy:new Float32Array(t),particles:new Float32Array(t*4),seeds:new Float32Array(t*4)};for(let i=0;i<t;i++){const R=g[i],ne=-o*A+(R%L+.5+(x()-.5)*.85)/L*l,oe=-s*A+(Math.floor(R/L)+.5+(x()-.5)*.85)/X*$;d.homeX[i]=ne,d.homeY[i]=oe,d.band[i]=x()*2-1,d.phase[i]=x()*Math.PI*2,d.agility[i]=.75+x()*.5,d.delay[i]=x(),m&&i<m.total?(d.x[i]=m.x[i]/m.width*o,d.y[i]=m.y[i]/m.height*s,d.vx[i]=m.vx[i],d.vy[i]=m.vy[i],d.orbit[i]=m.orbit[i]):(d.x[i]=ne,d.y[i]=H===0?oe+s*(.45+d.delay[i]*.4):oe),d.seeds[i*4]=I[i],d.seeds[i*4+1]=d.phase[i],d.seeds[i*4+2]=x(),d.seeds[i*4+3]=x()}return e.bindBuffer(e.ARRAY_BUFFER,Ee),e.bufferData(e.ARRAY_BUFFER,d.seeds,e.STATIC_DRAW),e.bindBuffer(e.ARRAY_BUFFER,ce),e.bufferData(e.ARRAY_BUFFER,d.particles.byteLength,e.DYNAMIC_DRAW),d};let Me=!1;const bt=()=>{const t=q.current,o=U.getBoundingClientRect(),s=Math.max(1,o.width),m=Math.max(1,o.height),x=Math.min(window.devicePixelRatio||1,t.maxRatio,Math.sqrt(Lt/(s*m))),A=Math.max(1,Math.round(s*x)),l=Math.max(1,Math.round(m*x));(b.width!==A||b.height!==l)&&(b.width=A,b.height=l),E.width=s,E.height=m,E.ratio=A/s,f.ready||(f.x=f.tx=s/2,f.y=f.ty=m/2,f.ready=!0),(!S||S.total!==t.count||Math.abs(S.width-s)>.5||Math.abs(S.height-m)>.5)&&(S=Rt(t.count,s,m,S))},Re=()=>{Me=!0,W()},St=(t,o)=>{if(!S)return;const s=E.height/Le,m=t.ring*s*(1-.38*C),x=Math.max(t.magnet*s,1),A=5/(t.tightness+.1)*s,l=t.waveAmplitude*s*.5,$=2.2+t.follow*80,L=(t.rotation+C*3)*(te?.3:1),X=E.width/2,g=E.height/2,I=x*.18,{homeX:d,homeY:i,band:R,phase:ne,agility:oe,delay:Pt,orbit:Je,x:Se,y:Te,vx:he,vy:me,particles:pe,seeds:Ft,total:_t}=S;for(let u=0;u<_t;u++){const Qe=1-Ft[u*4]*.12*t.depth,et=X+(f.x-X)*Qe,tt=g+(f.y-g)*Qe,nt=d[u],ot=i[u]+Math.sin(O*.7+ne[u])*s*.12*t.waveAmplitude,Pe=nt-et,Fe=ot-tt,_e=Math.sqrt(Pe*Pe+Fe*Fe),Be=1-P((_e-(x-I))/(I*2),0,1),ve=Be*Be*(3-2*Be);let j=Je[u]+L*ve*o;j-=Math.atan2(Math.sin(j),Math.cos(j))*(1-ve)*Math.min(1,o*2.5),Je[u]=j;let rt=nt,at=ot;if(ve>0||Math.abs(j)>1e-4){const Z=Math.atan2(Fe,Pe)+j,Bt=l*(.6*Math.sin(Z*t.waveCount-O*t.waveSpeed*2)+.4*Math.sin(ne[u]+O*t.waveSpeed*1.3)),lt=_e+(m+Bt+R[u]*A-_e)*ve;rt=et+Math.cos(Z)*lt,at=tt+Math.sin(Z)*lt}const xe=H<Pt[u]*.9?0:$*oe[u],it=1.64*xe,st=xe*xe,ct=Math.min(4,Math.max(1,Math.ceil(xe*o*2))),ye=o/ct;for(let Z=0;Z<ct;Z++)he[u]+=((rt-Se[u])*st-he[u]*it)*ye,me[u]+=((at-Te[u])*st-me[u]*it)*ye,Se[u]+=he[u]*ye,Te[u]+=me[u]*ye;pe[u*4]=Se[u],pe[u*4+1]=Te[u],pe[u*4+2]=he[u],pe[u*4+3]=me[u]}},Tt=(t,o)=>{if(!S)return;const s=E.height/Le,m=Math.max(t.magnet,t.ring)*s*1.8,{x,y:A,vx:l,vy:$,agility:L,total:X}=S;for(let g=0;g<X;g++){const I=x[g]-f.x,d=A[g]-f.y,i=Math.sqrt(I*I+d*d)+.001,R=s*110*o*Math.exp(-Math.pow(i/m,2))*L[g];l[g]+=I/i*R,$[g]+=d/i*R}},be=t=>{if(D=0,!k){if(K||!ee||e.isContextLost())return;if(!e.getProgramParameter(V.program,ee.COMPLETION_STATUS_KHR)){G=Math.max(G,t),ue&&fe&&(D=requestAnimationFrame(be));return}if(k=pt(e,V),!k){K=!0;return}}if(Me&&(Me=!1,bt()),K||!S)return;const o=q.current;Mt(o);const s=P((t-G)/1e3||0,0,1/30);G=Math.max(G,t);const m=te?.3:1;O+=s*m,qe+=s*o.colorSpeed*m,H<3&&(H+=s),C=v.down?Math.min(1,C+s/.9):Math.max(0,C-s*4);const x=performance.now()-v.moved>2e3||!v.inside;o.autoAnimate&&!te&&(x||!o.interactive)?(f.tx=E.width/2+Math.sin(O*.5)*(E.width/4),f.ty=E.height/2+Math.cos(O)*(E.height/4)):o.interactive&&v.inside&&(f.tx=v.x,f.ty=v.y),f.vx+=((f.tx-f.x)*49-f.vx*14)*s,f.vy+=((f.ty-f.y)*49-f.vy*14)*s,f.x+=f.vx*s,f.y+=f.vy*s,St(o,s);const A=E.height/Le;e.viewport(0,0,b.width,b.height),e.clearColor(0,0,0,0),e.clear(e.COLOR_BUFFER_BIT),e.enable(e.BLEND),e.blendFunc(e.ONE,e.ONE_MINUS_SRC_ALPHA),e.useProgram(k.program),e.bindVertexArray(we),e.bindBuffer(e.ARRAY_BUFFER,ce),e.bufferSubData(e.ARRAY_BUFFER,0,S.particles);const l=k.uniforms;e.activeTexture(e.TEXTURE0),e.bindTexture(e.TEXTURE_2D,le),e.uniform1i(l.uPalette,0),e.uniform2f(l.uSize,E.width,E.height),e.uniform1f(l.uPixel,E.ratio),e.uniform2f(l.uCursor,f.x,f.y),e.uniform1f(l.uRing,o.ring*A*(1-.38*C)),e.uniform1f(l.uBand,Math.max(A*6,1)),e.uniform1f(l.uParticle,o.size*A*.6),e.uniform1f(l.uAmbient,o.ambient),e.uniform1f(l.uTime,O),e.uniform1f(l.uPulse,o.pulse),e.uniform1f(l.uVariance,o.variance),e.uniform1f(l.uDepth,o.depth),e.uniform1f(l.uShape,o.shape),e.uniform1f(l.uColorMode,o.colorMode),e.uniform1f(l.uStops,o.palette.length),e.uniform1f(l.uHue,qe),e.uniform1f(l.uReveal,H<3?P(H/.6,0,1):1),e.uniform1f(l.uStreak,A*30),e.uniform1f(l.uCharge,C),e.uniform1f(l.uOpacity,o.opacity),e.drawArraysInstanced(e.TRIANGLE_STRIP,0,4,S.total),ue&&fe&&(D=requestAnimationFrame(be))},W=()=>{D||K||!ue||!fe||(G=performance.now(),D=requestAnimationFrame(be))};Q.current=()=>{Re(),W()};const Ve=t=>{const o=U.getBoundingClientRect();return{x:t.clientX-o.left,y:t.clientY-o.top,inside:t.clientX>=o.left&&t.clientX<=o.right&&t.clientY>=o.top&&t.clientY<=o.bottom}},ke=t=>{const o=Ve(t);v.inside=o.inside,o.inside&&(v.x=o.x,v.y=o.y,v.moved=performance.now())},He=t=>{const o=q.current,s=Ve(t);!s.inside||!o.interactive||(v.x=s.x,v.y=s.y,v.inside=!0,v.moved=performance.now(),o.clickBurst&&!te&&(v.down=!0))},de=t=>{v.down&&Tt(q.current,.45+C),v.down=!1,t.pointerType!=="mouse"&&(v.inside=!1)},Ge=()=>{v.inside=!1,v.down=!1};window.addEventListener("pointermove",ke,{passive:!0}),window.addEventListener("pointerdown",He,{passive:!0}),window.addEventListener("pointerup",de,{passive:!0}),window.addEventListener("pointercancel",de,{passive:!0}),document.documentElement.addEventListener("pointerleave",Ge);const Ke=t=>{t.preventDefault(),K=!0,cancelAnimationFrame(D),D=0},We=()=>Et(t=>t+1);b.addEventListener("webglcontextlost",Ke),b.addEventListener("webglcontextrestored",We);const $e=new ResizeObserver(()=>{Re(),W()});$e.observe(U);const je=new IntersectionObserver(t=>{ue=t.some(o=>o.isIntersecting),W()});je.observe(U);const Ze=()=>{fe=document.visibilityState!=="hidden",W()};return document.addEventListener("visibilitychange",Ze),Re(),W(),()=>{var t;cancelAnimationFrame(D),Q.current=null,$e.disconnect(),je.disconnect(),window.removeEventListener("pointermove",ke),window.removeEventListener("pointerdown",He),window.removeEventListener("pointerup",de),window.removeEventListener("pointercancel",de),document.documentElement.removeEventListener("pointerleave",Ge),document.removeEventListener("visibilitychange",Ze),b.removeEventListener("webglcontextlost",Ke),b.removeEventListener("webglcontextrestored",We),e.isContextLost()||(e.deleteBuffer(Ye),e.deleteBuffer(ce),e.deleteBuffer(Ee),e.deleteTexture(le),e.deleteVertexArray(we),b.isConnected?K||vt(e,ee,V):(t=e.getExtension("WEBGL_lose_context"))==null||t.loseContext())}},[ze]),React.createElement("div",{ref:De,className:`antigravity${Ce?` ${Ce}`:""}`,style:wt},React.createElement("canvas",{ref:Ie,className:"antigravity__canvas","aria-hidden":"true"}))};export{Vt as default};
