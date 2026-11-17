import{r as G}from"./w13-react.js";const yt=12,Tt=3,ge=yt+Tt,Q=128,Xt=`
const vec2 corners[3] = vec2[3](vec2(-1.0, -1.0), vec2(3.0, -1.0), vec2(-1.0, 3.0));
void main() {
  gl_Position = vec4(corners[gl_VertexID], 0.0, 1.0);
}`,Vt=`
layout(location = 0) in vec2 aSegment;
uniform mat3 uForward;
uniform vec2 uResolution;
uniform float uUnit;
uniform vec2 uCenter;
uniform vec4 uShape[SLOTS];
uniform vec4 uFrame[SLOTS];
uniform float uReach;
uniform float uSplit;
flat out int vSlot;
out vec2 vPlane;
void main() {
  vec4 shape = uShape[gl_InstanceID];
  vec2 origin = uCenter + uFrame[gl_InstanceID].xy;
  float angle = aSegment.x * 6.2831853;
  vec2 direction = vec2(cos(angle), sin(angle));
  vec3 inner = uForward * vec3(origin + direction * shape.x, 1.0);
  vec3 outer = uForward * vec3(origin + direction * (shape.x + 0.01), 1.0);
  float pixels = length(outer.xy / outer.z - inner.xy / inner.z) * 100.0 * uUnit;
  float band = uReach / max(pixels, 1e-3) + uSplit * shape.x;
  vec2 plane = origin + direction * max(shape.x + aSegment.y * band, 0.0);
  vec3 screen = uForward * vec3(plane, 1.0);
  gl_Position = vec4(screen.xy * (2.0 * uUnit / uResolution), 0.0, screen.z);
  vPlane = plane;
  vSlot = gl_InstanceID;
}`,Wt=`
uniform vec2 uResolution;
uniform vec2 uScale;
uniform float uUnit;
uniform mat3 uInverse;
uniform vec2 uCenter;
uniform int uCount;
uniform vec4 uShape[SLOTS];
uniform vec4 uFrame[SLOTS];
uniform vec4 uTint[SLOTS];
uniform vec4 uHover;
uniform vec2 uBounds;
uniform float uSplit;
uniform float uOpacity;

out vec4 fragColor;

float hash(vec2 p) {
  vec3 q = fract(vec3(p.xyx) * 0.1031);
  q += dot(q, q.yzx + 33.33);
  return fract((q.x + q.y) * q.z);
}

float angleOf(vec2 v) {
  vec2 a = abs(v);
  float t = min(a.x, a.y) / max(max(a.x, a.y), 1e-6);
  float r = t * (0.5 + 0.1738 * (1.0 - t));
  return a.y > a.x ? 1.0 - r : r;
}

float nearness(vec2 plane) {
  vec2 offset = plane - uHover.xy;
  return uHover.z * exp(-dot(offset, offset) * uHover.w);
}

vec3 prismOf(vec3 tint) {
  return mix(tint, vec3(dot(tint, vec3(0.3333))), 0.65) * 0.85;
}

vec4 toneMap(vec3 light) {
  float peak = max(max(light.r, light.g), light.b);
  if (peak <= 0.0) return vec4(0.0);
  float level = 1.0 - exp(-peak);
  vec3 color = mix(light * (level / peak), vec3(level), smoothstep(1.4, 3.6, peak) * 0.4);
  return vec4(color, level);
}
`,qt=`
uniform vec4 uHalo;
uniform vec3 uRange;

float haloOf(float d) {
  float fall = exp(-d * uHalo.y);
  float tight = fall * fall;
  tight *= tight;
  float window = clamp(1.0 - (d - uRange.x) * uRange.y, 0.0, 1.0);
  return (uHalo.z * tight + uHalo.w * fall) * window * window;
}

void main() {
  vec3 projected = uInverse * vec3((gl_FragCoord.xy * uScale - 0.5 * uResolution) / uUnit, 1.0);
  vec2 plane = projected.xy / projected.z;
  vec2 base = plane - uCenter;
  float reach = length(base);
  if (projected.z <= 0.0 || reach < uBounds.x || reach > uBounds.y) {
    fragColor = vec4(0.0);
    return;
  }
  float boost = 1.0 + 1.6 * nearness(plane);
  float limit = uRange.z + uSplit;
  vec3 light = vec3(0.0);
  float alpha = 0.0;

  for (int i = 0; i < SLOTS; i++) {
    if (i >= uCount) break;
    vec4 shape = uShape[i];
    vec4 frame = uFrame[i];
    vec2 w = base - frame.xy;
    float len = length(w);
    float d = abs(len - shape.x);
    if (d > limit) continue;
    float a = angleOf(vec2(dot(w, frame.zw), frame.z * w.y - frame.w * w.x));
    if (a > shape.w) continue;
    float energy = shape.y * max(exp(-shape.z * a * a * a) - 0.002, 0.0) * boost;
    vec3 tint = uTint[i].rgb;
#ifdef PRISM
    float offset = uSplit * shape.x;
    vec3 halo = vec3(haloOf(abs(len - shape.x - offset)), haloOf(d), haloOf(abs(len - shape.x + offset)));
#else
    vec3 halo = vec3(haloOf(d));
#endif
#ifdef LIGHT
    float layer = clamp(energy * dot(halo, vec3(0.3333)), 0.0, 1.0);
    light = tint * layer + light * (1.0 - layer);
    alpha = layer + alpha * (1.0 - layer);
#else
#ifdef PRISM
    tint = prismOf(tint);
#endif
    light += tint * energy * halo;
#endif
  }
#ifdef LIGHT
  fragColor = vec4(light, alpha);
#elif defined(PACKED)
  fragColor = vec4(1.0 - exp(-light), 1.0);
#else
  fragColor = vec4(light, 1.0);
#endif
}`,jt=`
uniform sampler2D uGlow;
uniform float uGrain;
uniform float uTime;

void main() {
  vec4 glow = texture(uGlow, gl_FragCoord.xy / uResolution);
  float grain = 1.0;
  if (uGrain > 0.0) grain += uGrain * (hash(gl_FragCoord.xy + fract(uTime * 7.31) * 413.0) - 0.5) * 1.8;
  float dither = (hash(gl_FragCoord.xy) - 0.5) / 255.0;
#ifdef LIGHT
  float alpha = clamp(glow.a * grain, 0.0, 1.0);
  if (alpha <= 0.0) {
    fragColor = vec4(0.0);
    return;
  }
  vec3 ink = glow.rgb * grain;
  alpha = clamp(alpha + dither, 0.0, 1.0);
  fragColor = vec4(min(ink, vec3(alpha)), alpha) * uOpacity;
#else
#ifdef PACKED
  vec3 light = -log(1.0 - min(glow.rgb, vec3(0.9995))) * grain;
#else
  vec3 light = glow.rgb * grain;
#endif
  vec4 color = toneMap(light);
  if (color.a <= 0.0) {
    fragColor = vec4(0.0);
    return;
  }
  float alpha = clamp(color.a + dither, 0.0, 1.0);
  fragColor = vec4(min(color.rgb, vec3(alpha)), alpha) * uOpacity;
#endif
}`,Yt=`
flat in int vSlot;
in vec2 vPlane;
uniform float uWidth;

float coverage(float pixels, float halfWidth) {
  return clamp(max(halfWidth, 0.5) - pixels + 0.5, 0.0, 1.0) * min(halfWidth * 2.0, 1.0);
}

void main() {
  vec4 shape = uShape[vSlot];
  vec4 frame = uFrame[vSlot];
  vec4 tint = uTint[vSlot];
  vec2 w = vPlane - uCenter - frame.xy;
  float len = length(w);
  float stretch = 1.0 / max(length(vec2(dFdx(len), dFdy(len))), 1e-6);
  float a = angleOf(vec2(dot(w, frame.zw), frame.z * w.y - frame.w * w.x));
  float near = nearness(vPlane);
  float energy = shape.y * max(exp(-shape.z * a * a * a) - 0.002, 0.0) * (1.0 + 1.6 * near) * step(a, shape.w);
  float halfWidth = uWidth * (1.0 + 0.45 * near) * max(1.0 - a, 0.5) * tint.w;
  float pixels = abs(len - shape.x) * stretch;
#ifdef PRISM
  float offset = uSplit * shape.x;
  vec3 core = vec3(
    coverage(abs(len - shape.x - offset) * stretch, halfWidth),
    coverage(pixels, halfWidth),
    coverage(abs(len - shape.x + offset) * stretch, halfWidth)
  );
#else
  vec3 core = vec3(coverage(pixels, halfWidth));
#endif
#ifdef LIGHT
  float hot = core.g * clamp(1.0 - pixels / max(halfWidth * 0.35, 0.3), 0.0, 1.0);
  float layer = energy * max(max(core.r, core.g), core.b);
  if (layer <= 0.0) discard;
#ifdef PRISM
  vec3 color = clamp(1.0 - (1.0 - tint.rgb) * core * 1.4, 0.0, 1.0);
#else
  vec3 color = tint.rgb;
#endif
  color = mix(color, vec3(1.0), hot * 0.7);
  fragColor = vec4(color * layer, layer) * uOpacity;
#else
#ifdef PRISM
  vec4 color = toneMap(prismOf(tint.rgb) * energy * core * 1.7);
#else
  vec4 color = toneMap(tint.rgb * energy * core * 1.7);
#endif
  if (color.a <= 0.0) discard;
  fragColor = color * uOpacity;
#endif
}`,xt=(n,s)=>["#version 300 es","precision highp float;",`#define SLOTS ${ge}`,...s.map(i=>`#define ${i}`),n].join(`
`),$t={glow:qt,compose:jt,line:Yt},Kt=["glow","compose","line"],Jt=["pulse","ripple","breathe"],z=3.45,wt=.5,Et=2.6,ve=1.1,St=.9,A=(n,s,i)=>Math.min(i,Math.max(s,n)),q=(n,s,i)=>{const r=A((i-n)/(s-n),0,1);return r*r*(3-2*r)},Qt=n=>Math.min(q(0,.7,n),1-q(.5,3.25,n)),Mt=n=>n<=.04045?n/12.92:Math.pow((n+.055)/1.055,2.4),Ce=n=>{const s=String(n||"").trim(),i=/^#?([0-9a-f]{3}|[0-9a-f]{6})$/i.exec(s);if(i){const l=i[1].length===3?i[1].replace(/./g,f=>f+f):i[1];return[0,2,4].map(f=>parseInt(l.slice(f,f+2),16)/255)}const r=/^rgba?\(\s*([\d.]+)[\s,]+([\d.]+)[\s,]+([\d.]+)/i.exec(s);if(r)return[r[1],r[2],r[3]].map(l=>A(parseFloat(l)/255,0,1));if(typeof document<"u"){const l=document.createElement("canvas").getContext("2d");if(l){l.fillStyle="#000000",l.fillStyle=s;const f=String(l.fillStyle);if(f!=="#000000"||/^(#0{3}|#0{6}|black)$/i.test(s))return Ce(f)}}return[.66,.33,.97]},Rt=(n,s,i)=>{const r=n.createShader(s);return r?(n.shaderSource(r,i),n.compileShader(r),r):null},Zt=(n,s,i)=>{const r=Rt(n,n.VERTEX_SHADER,s),l=Rt(n,n.FRAGMENT_SHADER,i),f=r&&l?n.createProgram():null;return!r||!l||!f?(r&&n.deleteShader(r),l&&n.deleteShader(l),null):(n.attachShader(f,r),n.attachShader(f,l),n.linkProgram(f),{program:f,vertex:r,fragment:l})},en=(n,s)=>{const{program:i,vertex:r,fragment:l}=s,f=n.getProgramParameter(i,n.LINK_STATUS);if(!f&&!n.isContextLost()){const x=[r,l].filter(E=>!n.getShaderParameter(E,n.COMPILE_STATUS));x.length?x.forEach(E=>console.error(n.getShaderInfoLog(E))):console.error(n.getProgramInfoLog(i))}if(n.deleteShader(r),n.deleteShader(l),!f)return n.deleteProgram(i),null;const b={},F=n.getProgramParameter(i,n.ACTIVE_UNIFORMS);for(let x=0;x<F;x++){const E=n.getActiveUniform(i,x);E&&(b[E.name.replace(/\[0\]$/,"")]=n.getUniformLocation(i,E.name))}return{program:i,uniforms:b}},tn=n=>{const[s,i,r,l,f,b,F,x,E]=n,Z=f*E-b*x,ee=b*F-l*E,te=l*x-f*F,xe=s*Z+i*ee+r*te;return[Z,r*x-i*E,i*b-r*f,ee,s*E-r*F,r*l-s*b,te,i*F-s*x,s*f-i*l].map(we=>we/xe)},nn=(n,s)=>{const i=n*Math.PI/180,r=s*Math.PI/180,l=Math.cos(i),f=Math.sin(i),b=Math.cos(r),F=Math.sin(r),x=[b,F*f,0,0,l,0,F*l/Et,-f/Et,1];return{forward:x,inverse:tn(x)}},on=(n,s,i)=>{const r=n[6]*s+n[7]*i+n[8];return[(n[0]*s+n[1]*i+n[2])/r,(n[3]*s+n[4]*i+n[5])/r,r]},an=({color:n="#A855F7",colorTwo:s="#6366F1",ringCount:i=6,motion:r="pulse",speed:l=1,lineThickness:f=2,glow:b=1,baseRadius:F=.35,radiusStep:x=.1,expansion:E=.1,taper:Z=1.5,rotation:ee=0,spin:te=0,tilt:xe=0,dispersion:we=0,grain:bt=0,opacity:Ft=1,followMouse:At=!1,mouseInfluence:Lt=.2,hoverScale:_t=1.2,hoverIntensity:Ct=1,parallax:It=.05,clickBurst:Pt=!0,intro:Ot=!0,theme:Ut="dark",maxDevicePixelRatio:Bt=2,className:Ie="",style:Dt})=>{const Pe=G.useRef(null),Oe=G.useRef(null),j=G.useRef(null),ne=G.useRef(0),[Ue,Gt]=G.useState(0),Ee=Ut==="light",Be=Ce(n),De=Ce(s),Ge={color:Ee?Be:Be.map(Mt),colorTwo:Ee?De:De.map(Mt),ringCount:A(Math.round(i),1,yt),motion:Jt.includes(r)?r:"pulse",speed:Math.max(0,l),lineThickness:Math.max(.25,f),glow:A(b,0,3),baseRadius:Math.max(.01,F),radiusStep:Math.max(0,x),expansion:E,taper:Math.max(.5,Z),rotation:ee*Math.PI/180,spin:te,tilt:A(xe,-80,80),dispersion:A(we,0,1),grain:A(bt,0,1),opacity:A(Ft,0,1),followMouse:At,mouseInfluence:Lt,hoverScale:Math.max(.5,_t),hoverIntensity:Math.max(0,Ct),parallax:It,clickBurst:Pt,intro:Ot,light:Ee,maxRatio:A(Bt,.5,3)},C=G.useRef(Ge);return C.current=Ge,G.useEffect(()=>{var I;(I=j.current)==null||I.call(j)}),G.useEffect(()=>{const I=Pe.current,m=Oe.current;if(!I||!m)return;cancelAnimationFrame(ne.current),ne.current=0;const e=m.getContext("webgl2",{alpha:!0,premultipliedAlpha:!0,antialias:!1,depth:!1,stencil:!1,powerPreference:"default"});if(!e)return;const oe=e.getExtension("KHR_parallel_shader_compile"),Y=new Map,Ne=t=>!oe||t.jobs.every(o=>!o||e.getProgramParameter(o.program,oe.COMPLETION_STATUS_KHR)),He=t=>{t.programs=t.jobs.map(o=>o&&en(e,o)),t.jobs=[],t.pending=!1},ke=t=>{const o=t.join(",");let a=Y.get(o);return a||(a={jobs:Kt.map(u=>Zt(e,xt(u==="line"?Vt:Xt,t),xt(`${Wt}${$t[u]}`,t))),programs:[],pending:!0},Y.set(o,a),oe||He(a)),a};if(!oe&&ke([]).programs.includes(null))return;const $=window.matchMedia("(prefers-reduced-motion: reduce)").matches,ze=e.createVertexArray(),Se=e.createVertexArray(),Xe=e.createBuffer(),Ve=new Float32Array((Q+1)*4);for(let t=0;t<=Q;t++)Ve.set([t/Q,-1,t/Q,1],t*4);e.bindVertexArray(Se),e.bindBuffer(e.ARRAY_BUFFER,Xe),e.bufferData(e.ARRAY_BUFFER,Ve,e.STATIC_DRAW),e.enableVertexAttribArray(0),e.vertexAttribPointer(0,2,e.FLOAT,!1,0,0),e.bindVertexArray(null);const re=e.createTexture(),Me=e.createFramebuffer(),N={width:0,height:0};let Re=!!e.getExtension("EXT_color_buffer_float");const We=new Float32Array(ge*4),qe=new Float32Array(ge*4),je=new Float32Array(ge*4),H={width:1,height:1,ratio:1,unit:1},p={x:0,y:0,inside:!1,touch:!1},d={x:0,y:0,vx:0,vy:0,hover:0,hoverVelocity:0},L=[];let P=C.current.intro&&C.current.speed>0&&!$&&Ue===0,O=P?0:1.6,X=0,U=0,V=0,ae=!0,ie=document.visibilityState!=="hidden",se=!1,ce=null,Ye=C.current;const ye=t=>{const o=C.current,a=[];o.light&&a.push("LIGHT"),o.dispersion>0&&a.push("PRISM"),Re||a.push("PACKED");const c=ke(a);return t&&c.pending&&Ne(c)&&He(c),c.pending||(ce=c,Ye=o),c.pending},$e=()=>{const t=C.current,o=I.getBoundingClientRect(),a=Math.min(window.devicePixelRatio||1,t.maxRatio),c=Math.max(1,Math.round(o.width*a)),u=Math.max(1,Math.round(o.height*a));(m.width!==c||m.height!==u)&&(m.width=c,m.height=u),Object.assign(H,{width:c,height:u,ratio:a,unit:Math.max(1,Math.min(c,u))});const R=Math.max(1,Math.ceil(c/2)),S=Math.max(1,Math.ceil(u/2));if(N.width===R&&N.height===S)return;Object.assign(N,{width:R,height:S});const y=T=>{e.bindTexture(e.TEXTURE_2D,re),e.texImage2D(e.TEXTURE_2D,0,T?e.RGBA16F:e.RGBA8,R,S,0,e.RGBA,T?e.HALF_FLOAT:e.UNSIGNED_BYTE,null),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_MIN_FILTER,e.LINEAR),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_MAG_FILTER,e.LINEAR),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_WRAP_S,e.CLAMP_TO_EDGE),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_WRAP_T,e.CLAMP_TO_EDGE),e.bindFramebuffer(e.FRAMEBUFFER,Me),e.framebufferTexture2D(e.FRAMEBUFFER,e.COLOR_ATTACHMENT0,e.TEXTURE_2D,re,0);const w=e.checkFramebufferStatus(e.FRAMEBUFFER)===e.FRAMEBUFFER_COMPLETE;return e.bindFramebuffer(e.FRAMEBUFFER,null),w};y(Re)||(Re=!1,y(!1))},Nt=(t,o,a)=>{const c=a>1?o/(a-1):0;if(t.motion==="ripple"){const y=a*t.radiusStep+t.expansion,T=z*Math.max(1,a)*.32,w=O/T+o/a,M=w-Math.floor(w),g=O-M*T>=-1e-4||!P?q(0,.12,M)*(1-q(.62,1,M)):0,k=M*Math.max(1,a-1);return{radius:t.baseRadius+M*y,life:g,virtual:k,mix:M,opening:1}}if(t.motion==="breathe"){const y=Math.sin(O/4*Math.PI*2-o*.55),T=P?q(o*.12,o*.12+.9,O):1;return{radius:t.baseRadius+o*t.radiusStep+t.expansion*.5*(1+y),life:(.78+.22*y)*T,virtual:o,mix:c,opening:1}}const u=O-o*wt,R=(u%z+z)%z,S=P&&u<0?0:Qt(R);return{radius:t.baseRadius+o*t.radiusStep+(1-Math.pow(1-R/z,2))*t.expansion,life:S,virtual:o,mix:c,opening:P&&u<z?q(0,.9,u):1}},Ke=()=>{if(!ce)return;const t=Ye,o=t.ringCount,a=d.hover,c=$?0:1,u=1+(t.hoverScale-1)*a*c,R=d.x*c,S=d.y*c,y=t.tilt-S*t.parallax*260,T=R*t.parallax*260,w=nn(y,T),M=t.followMouse?[R*t.mouseInfluence,S*t.mouseInfluence]:[0,0],be=t.spin*O*.6;let g=0,k=1/0,fe=0,Fe=0;const st=(v,h,_,D,he,de,W,me)=>{if(h<=.002)return;const J=10*v*_*_*_;We.set([v,h,J,J>6.2?Math.cbrt(6.2/J):2],g*4),qe.set([he,de,Math.cos(D),Math.sin(D)],g*4),je.set([t.color[0]+(t.colorTwo[0]-t.color[0])*W,t.color[1]+(t.colorTwo[1]-t.color[1])*W,t.color[2]+(t.colorTwo[2]-t.color[2])*W,me],g*4),k=Math.min(k,v),fe=Math.max(fe,v),Fe=Math.max(Fe,Math.hypot(he,de)),g++};for(let v=0;v<o;v++){const h=Nt(t,v,o);let _=h.radius*u,D=h.life;for(const W of L){const me=X-W.start,J=(W.radius+me*St)*u,gt=Math.pow(Math.max(0,1-me/ve),1.5),pe=(J-_)/.08;_+=.04*Math.exp(-pe*pe)*gt,D=Math.min(1.6,D+.9*Math.exp(-pe*pe*1.6)*gt)}const he=Math.pow(t.taper,h.virtual)/Math.max(.08,h.opening),de=t.rotation+be*(v%2?-1:1)*(1+v*.15);st(_,D,he,de,v*t.parallax*R,v*t.parallax*S,h.mix,1)}for(const v of L){const h=X-v.start,_=Math.pow(Math.max(0,1-h/ve),1.5),D=(v.radius+h*St)*u;st(D,1.5*_,.85,t.rotation,0,0,A(h/ve,0,1),1.15)}if(e.bindFramebuffer(e.FRAMEBUFFER,null),e.viewport(0,0,m.width,m.height),e.clearColor(0,0,0,0),e.clear(e.COLOR_BUFFER_BIT),!g)return;const[ct,lt,ft]=ce.programs;if(!ct||!lt||!ft)return;const ut=(t.light?.3:1)*(1-t.dispersion*.45),ht=.012+.033*t.glow,Ht=.5*Math.min(1,t.glow)*ut,kt=.9*t.glow*ut,ue=ht*4.5+.008,dt=t.dispersion*.03,mt=ue+dt*fe+Fe+.01,pt=t.lineThickness*H.ratio,zt=pt*(1+.45*t.hoverIntensity)*1.15+1.5,vt=on(w.inverse,p.x,p.y),Ae=v=>{const h=v.uniforms;return e.useProgram(v.program),e.uniform2f(h.uResolution,m.width,m.height),e.uniform1f(h.uUnit,H.unit),e.uniform2f(h.uCenter,M[0],M[1]),e.uniform1i(h.uCount,g),e.uniform4fv(h.uShape,We),e.uniform4fv(h.uFrame,qe),e.uniform4fv(h.uTint,je),e.uniform4f(h.uHover,vt[0]-M[0],vt[1]-M[1],a*t.hoverIntensity,1/.03),e.uniform1f(h.uSplit,dt),e.uniform1f(h.uOpacity,t.opacity),h};e.bindVertexArray(ze),e.bindFramebuffer(e.FRAMEBUFFER,Me),e.viewport(0,0,N.width,N.height),e.clear(e.COLOR_BUFFER_BIT);const K=Ae(ct);e.uniform2f(K.uScale,m.width/N.width,m.height/N.height),e.uniformMatrix3fv(K.uInverse,!0,w.inverse),e.uniform2f(K.uBounds,Math.max(0,k-mt),fe+mt),e.uniform4f(K.uHalo,0,1/ht,Ht,kt),e.uniform3f(K.uRange,ue*.45,1/(ue*.55),ue),e.drawArrays(e.TRIANGLES,0,3),e.bindFramebuffer(e.FRAMEBUFFER,null),e.viewport(0,0,m.width,m.height);const Le=Ae(lt);e.activeTexture(e.TEXTURE0),e.bindTexture(e.TEXTURE_2D,re),e.uniform1i(Le.uGlow,0),e.uniform1f(Le.uGrain,t.grain),e.uniform1f(Le.uTime,X),e.drawArrays(e.TRIANGLES,0,3);const _e=Ae(ft);e.uniformMatrix3fv(_e.uForward,!0,w.forward),e.uniform1f(_e.uReach,zt),e.uniform1f(_e.uWidth,pt),e.enable(e.BLEND),t.light?e.blendFunc(e.ONE,e.ONE_MINUS_SRC_ALPHA):e.blendFuncSeparate(e.ONE,e.ONE_MINUS_SRC_COLOR,e.ONE,e.ONE_MINUS_SRC_ALPHA),e.bindVertexArray(Se),e.drawArraysInstanced(e.TRIANGLE_STRIP,0,(Q+1)*2,g),e.disable(e.BLEND),e.bindVertexArray(null)},Te=t=>{if(U=0,se)return;const o=ye(!0);if(!ce){V=Math.max(V,t),ae&&ie&&(U=requestAnimationFrame(Te));return}const a=C.current,c=A((t-V)/1e3||0,0,.05);V=Math.max(V,t),X+=c,$||(O+=c*a.speed);const u=p.inside?p.x/Math.max(1e-6,H.width/H.unit):0,R=p.inside?p.y/Math.max(1e-6,H.height/H.unit):0,S=70,y=2*Math.sqrt(S)*.9,T=Math.max(1,Math.ceil(c/(1/240))),w=c/T;for(let g=0;g<T;g++){d.vx+=(S*(u-d.x)-y*d.vx)*w,d.vy+=(S*(R-d.y)-y*d.vy)*w,d.x+=d.vx*w,d.y+=d.vy*w;const k=p.inside?1:0;d.hoverVelocity+=(40*(k-d.hover)-2*Math.sqrt(40)*d.hoverVelocity)*w,d.hover+=d.hoverVelocity*w}for(let g=L.length-1;g>=0;g--)X-L[g].start>ve&&L.splice(g,1);P&&O>(a.ringCount+2)*wt+z&&(P=!1),Ke();const M=Math.abs(d.vx)+Math.abs(d.vy)+Math.abs(d.hoverVelocity)>1e-4||Math.abs((p.inside?1:0)-d.hover)>.001;(a.speed>0&&!$||M||L.length>0||P||o)&&ae&&ie&&(U=requestAnimationFrame(Te))},B=()=>{U||se||!ae||!ie||(V=performance.now(),U=requestAnimationFrame(Te))},Je=()=>{$e(),ye(!1),Ke(),B()};j.current=Je;const Qe=t=>{const o=I.getBoundingClientRect(),a=t.clientX>=o.left&&t.clientX<=o.right&&t.clientY>=o.top&&t.clientY<=o.bottom,c=Math.max(1,Math.min(o.width,o.height));return{x:(t.clientX-o.left-o.width/2)/c,y:(o.top+o.height/2-t.clientY)/c,inside:a}},Ze=t=>{if(t.pointerType==="touch"&&!p.touch)return;const o=Qe(t);p.x=o.x,p.y=o.y,p.inside=o.inside,B()},et=t=>{const o=Qe(t);if(!o.inside)return;p.x=o.x,p.y=o.y,p.inside=!0,p.touch=t.pointerType==="touch";const a=C.current;a.clickBurst&&!$&&(L.push({start:X,radius:Math.max(.04,a.baseRadius*.45)}),L.length>Tt&&L.shift()),B()},le=t=>{t.pointerType==="touch"&&(p.touch=!1,p.inside=!1,B())},tt=()=>{p.inside=!1,B()};window.addEventListener("pointermove",Ze,{passive:!0}),window.addEventListener("pointerdown",et,{passive:!0}),window.addEventListener("pointerup",le,{passive:!0}),window.addEventListener("pointercancel",le,{passive:!0}),document.documentElement.addEventListener("pointerleave",tt);const nt=t=>{t.preventDefault(),se=!0,cancelAnimationFrame(U),U=0},ot=()=>Gt(t=>t+1);m.addEventListener("webglcontextlost",nt),m.addEventListener("webglcontextrestored",ot);const rt=new ResizeObserver(Je);rt.observe(I);const at=new IntersectionObserver(t=>{ae=t.some(o=>o.isIntersecting),B()});at.observe(I);const it=()=>{ie=document.visibilityState!=="hidden",B()};return document.addEventListener("visibilitychange",it),$e(),ye(!1),B(),()=>{if(cancelAnimationFrame(U),j.current=null,rt.disconnect(),at.disconnect(),window.removeEventListener("pointermove",Ze),window.removeEventListener("pointerdown",et),window.removeEventListener("pointerup",le),window.removeEventListener("pointercancel",le),document.documentElement.removeEventListener("pointerleave",tt),document.removeEventListener("visibilitychange",it),m.removeEventListener("webglcontextlost",nt),m.removeEventListener("webglcontextrestored",ot),!e.isContextLost()){e.deleteVertexArray(ze),e.deleteTexture(re),e.deleteFramebuffer(Me),e.deleteVertexArray(Se),e.deleteBuffer(Xe);const o=()=>{se||e.isContextLost()||(Y.forEach((a,c)=>{a.pending&&!Ne(a)||(a.jobs.forEach(u=>{u&&(e.deleteShader(u.vertex),e.deleteShader(u.fragment),e.deleteProgram(u.program))}),a.programs.forEach(u=>u&&e.deleteProgram(u.program)),Y.delete(c))}),Y.size&&requestAnimationFrame(o))};o()}const t=()=>{var o;ne.current=0,!m.isConnected&&!e.isContextLost()&&((o=e.getExtension("WEBGL_lose_context"))==null||o.loseContext())};m.isConnected?ne.current=requestAnimationFrame(t):t()}},[Ue]),React.createElement("div",{ref:Pe,className:`magic-rings${Ie?` ${Ie}`:""}`,style:Dt},React.createElement("canvas",{ref:Oe,className:"magic-rings__canvas","aria-hidden":"true"}))};export{an as default};
