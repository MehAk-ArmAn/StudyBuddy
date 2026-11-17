import{r as I}from"./w13-react.js";const Oe=24e5,Ge=14e4,We=32,J=256,ze=`#version 300 es
const vec2 corners[3] = vec2[3](vec2(-1.0, -1.0), vec2(3.0, -1.0), vec2(-1.0, 3.0));
out vec2 vUv;
void main() {
  vUv = corners[gl_VertexID] * 0.5 + 0.5;
  gl_Position = vec4(corners[gl_VertexID], 0.0, 1.0);
}`,N=`#version 300 es
precision highp float;
precision highp sampler2D;
in vec2 vUv;
out vec4 fragColor;
uniform vec2 uTexel;
`,$e=`${N}
uniform sampler2D uVelocity;
uniform vec2 uSize;
uniform vec4 uSegment;
uniform vec2 uForce;
uniform float uRadius;

float segment(vec2 p, vec2 a, vec2 b) {
  vec2 ab = b - a;
  float t = clamp(dot(p - a, ab) / max(dot(ab, ab), 1e-4), 0.0, 1.0);
  return length(p - a - ab * t);
}

void main() {
  float d = segment(vUv * uSize, uSegment.xy, uSegment.zw) / uRadius;
  vec2 velocity = texture(uVelocity, vUv).xy + uForce * exp(-d * d * 6.0);
  fragColor = vec4(velocity, 0.0, 1.0);
}`,Ye=`${N}
uniform sampler2D uVelocity;
uniform float uAmount;

void main() {
  vec2 center = texture(uVelocity, vUv).xy;
  vec2 around =
    texture(uVelocity, vUv - vec2(uTexel.x, 0.0)).xy +
    texture(uVelocity, vUv + vec2(uTexel.x, 0.0)).xy +
    texture(uVelocity, vUv - vec2(0.0, uTexel.y)).xy +
    texture(uVelocity, vUv + vec2(0.0, uTexel.y)).xy;
  fragColor = vec4(mix(center, around * 0.25, uAmount), 0.0, 1.0);
}`,He=`${N}
uniform sampler2D uVelocity;
uniform float uWalls;

void main() {
  vec2 center = texture(uVelocity, vUv).xy;
  float left = texture(uVelocity, vUv - vec2(uTexel.x, 0.0)).x;
  float right = texture(uVelocity, vUv + vec2(uTexel.x, 0.0)).x;
  float down = texture(uVelocity, vUv - vec2(0.0, uTexel.y)).y;
  float up = texture(uVelocity, vUv + vec2(0.0, uTexel.y)).y;
  float edge = uWalls > 0.5 ? -1.0 : 0.0;
  if (vUv.x - uTexel.x < 0.0) left = edge * center.x;
  if (vUv.x + uTexel.x > 1.0) right = edge * center.x;
  if (vUv.y - uTexel.y < 0.0) down = edge * center.y;
  if (vUv.y + uTexel.y > 1.0) up = edge * center.y;
  fragColor = vec4(0.5 * (right - left + up - down), 0.0, 0.0, 1.0);
}`,Pe=`
uniform float uWalls;

float pressureAt(sampler2D field, vec2 uv) {
  vec2 inside = step(vec2(0.0), uv) * step(uv, vec2(1.0));
  return texture(field, uv).x * mix(inside.x * inside.y, 1.0, uWalls);
}
`,Ke=`${N}
uniform sampler2D uPressure;
uniform sampler2D uDivergence;
${Pe}
void main() {
  vec2 reach = uTexel * 2.0;
  float sum =
    pressureAt(uPressure, vUv - vec2(reach.x, 0.0)) +
    pressureAt(uPressure, vUv + vec2(reach.x, 0.0)) +
    pressureAt(uPressure, vUv - vec2(0.0, reach.y)) +
    pressureAt(uPressure, vUv + vec2(0.0, reach.y));
  fragColor = vec4(sum * 0.25 - texture(uDivergence, vUv).x, 0.0, 0.0, 1.0);
}`,qe=`${N}
uniform sampler2D uPressure;
uniform sampler2D uVelocity;
${Pe}
void main() {
  float left = pressureAt(uPressure, vUv - vec2(uTexel.x, 0.0));
  float right = pressureAt(uPressure, vUv + vec2(uTexel.x, 0.0));
  float down = pressureAt(uPressure, vUv - vec2(0.0, uTexel.y));
  float up = pressureAt(uPressure, vUv + vec2(0.0, uTexel.y));
  vec2 velocity = texture(uVelocity, vUv).xy - 0.5 * vec2(right - left, up - down);
  fragColor = vec4(velocity, 0.0, 1.0);
}`,je=`${N}
uniform sampler2D uVelocity;
uniform vec2 uStep;
uniform float uKeep;
uniform float uWalls;

vec2 at(vec2 uv) {
  vec2 outside = max(-uv, uv - 1.0) / uTexel;
  float open = clamp(1.0 - max(outside.x, outside.y) / 1.5, 0.0, 1.0);
  return texture(uVelocity, uv).xy * mix(open, 1.0, uWalls);
}

void main() {
  vec2 start = vUv - at(vUv) * uStep;
  vec2 back = start + at(start) * uStep;
  vec2 corrected = vUv - (back - vUv) * 0.5;
  vec2 origin = corrected - at(corrected) * uStep;
  vec2 a = at(origin + vec2(-0.5, -0.5) * uTexel);
  vec2 b = at(origin + vec2(0.5, -0.5) * uTexel);
  vec2 c = at(origin + vec2(-0.5, 0.5) * uTexel);
  vec2 d = at(origin + vec2(0.5, 0.5) * uTexel);
  vec2 velocity = clamp(at(origin), min(min(a, b), min(c, d)), max(max(a, b), max(c, d)));
  fragColor = vec4(velocity * uKeep, 0.0, 1.0);
}`,Je=`${N}
uniform sampler2D uVelocity;
uniform sampler2D uPalette;
uniform vec2 uSimSize;
uniform float uSpeedScale;
uniform float uLight;
uniform vec4 uBackground;

vec2 velocityAt(vec2 uv) {
  vec2 st = uv * uSimSize - 0.5;
  vec2 i = floor(st);
  vec2 f = st - i;
  vec2 f2 = f * f;
  vec2 f3 = f2 * f;
  vec2 w0 = (-f3 + 3.0 * f2 - 3.0 * f + 1.0) / 6.0;
  vec2 w1 = (3.0 * f3 - 6.0 * f2 + 4.0) / 6.0;
  vec2 w2 = (-3.0 * f3 + 3.0 * f2 + 3.0 * f + 1.0) / 6.0;
  vec2 w3 = f3 / 6.0;
  vec2 g0 = w0 + w1;
  vec2 g1 = w2 + w3;
  vec2 h0 = (i - 0.5 + w1 / g0) / uSimSize;
  vec2 h1 = (i + 1.5 + w3 / g1) / uSimSize;
  vec2 a = texture(uVelocity, vec2(h0.x, h0.y)).xy;
  vec2 b = texture(uVelocity, vec2(h1.x, h0.y)).xy;
  vec2 c = texture(uVelocity, vec2(h0.x, h1.y)).xy;
  vec2 d = texture(uVelocity, vec2(h1.x, h1.y)).xy;
  return g0.y * (g0.x * a + g1.x * b) + g1.y * (g0.x * c + g1.x * d);
}

void main() {
  float speed = length(velocityAt(vUv)) * uSpeedScale;
  float level = speed < 0.75 ? speed : 0.75 + 0.25 * tanh((speed - 0.75) * 4.0);
  vec3 ink = texture(uPalette, vec2(level, 0.5)).rgb;
  float alpha = level;
  if (uLight > 0.5) {
    float hollow = smoothstep(0.7, 1.6, speed);
    alpha = smoothstep(0.08, 0.5, level) * mix(0.85, 0.1, hollow);
  } else {
    float hot = 1.0 - exp(-max(speed - 0.75, 0.0) * 1.2);
    ink = mix(ink, min(ink * 1.8 + 0.06, vec3(1.0)), hot);
  }
  vec3 color = ink * alpha + uBackground.rgb * uBackground.a * (1.0 - alpha);
  float coverage = alpha + uBackground.a * (1.0 - alpha);
  float grain = fract(52.9829189 * fract(dot(gl_FragCoord.xy, vec2(0.06711056, 0.00583715))));
  color += (grain - 0.5) / 255.0 * step(0.002, coverage);
  fragColor = vec4(clamp(color, vec3(0.0), vec3(coverage)), coverage);
}`,y=(n,d,l)=>Math.min(l,Math.max(d,n)),_e=(n,d)=>Math.pow(.5,d/(.35+n*n*12)),De=n=>{const d=String(n||"").trim(),l=/^#?([0-9a-f]{3}|[0-9a-f]{6})$/i.exec(d);if(l){const s=l[1].length===3?l[1].replace(/./g,m=>m+m):l[1];return[0,2,4].map(m=>parseInt(s.slice(m,m+2),16)/255)}const v=/^rgba?\(\s*([\d.]+)[\s,]+([\d.]+)[\s,]+([\d.]+)/i.exec(d);return v?[v[1],v[2],v[3]].map(s=>y(parseFloat(s)/255,0,1)):null},Ze=n=>n<=.04045?n/12.92:Math.pow((n+.055)/1.055,2.4),Qe=n=>{const d=[];n.forEach(s=>{const m=De(s);m&&d.push(m)}),d.length||d.push([1,1,1]);const l=d.map(s=>s.map(Ze)),v=new Uint8Array(J*4);for(let s=0;s<J;s++){const m=y(s/(J-1)*l.length-.5,0,l.length-1),M=Math.min(Math.floor(m),Math.max(l.length-2,0)),P=Math.min(M+1,l.length-1),R=m-M,Z=R*R*(3-2*R);for(let S=0;S<3;S++){const Q=l[M][S]+(l[P][S]-l[M][S])*Z;v[s*4+S]=Math.round(y(Q,0,1)*255)}v[s*4+3]=255}return v},Ae=(n,d,l)=>{const v=n.createShader(d);return v?(n.shaderSource(v,l),n.compileShader(v),n.getShaderParameter(v,n.COMPILE_STATUS)?v:(console.error(n.getShaderInfoLog(v)),n.deleteShader(v),null)):null},C=(n,d)=>{const l=Ae(n,n.VERTEX_SHADER,ze),v=Ae(n,n.FRAGMENT_SHADER,d),s=l&&v?n.createProgram():null;if(!l||!v||!s)return l&&n.deleteShader(l),v&&n.deleteShader(v),null;if(n.attachShader(s,l),n.attachShader(s,v),n.linkProgram(s),n.deleteShader(l),n.deleteShader(v),!n.getProgramParameter(s,n.LINK_STATUS))return console.error(n.getProgramInfoLog(s)),n.deleteProgram(s),null;const m={},M=n.getProgramParameter(s,n.ACTIVE_UNIFORMS);for(let P=0;P<M;P++){const R=n.getActiveUniform(s,P);R&&(m[R.name]=n.getUniformLocation(s,R.name))}return{program:s,uniforms:m}},tt=({colors:n=["#5227FF","#FF9FFC","#B497CF"],intensity:d=1,mouseForce:l=20,cursorSize:v=100,persistence:s=.75,viscosity:m=0,resolution:M=.5,bounceEdges:P=!1,clickBurst:R=!0,autoDemo:Z=!0,autoSpeed:S=.5,autoIntensity:Q=2.2,autoResumeDelay:Le=1e3,intro:Fe=!0,lightMode:Ve=!1,backgroundColor:ce="transparent",maxDevicePixelRatio:Xe=2,className:se="",style:Ie})=>{const ue=I.useRef(null),le=I.useRef(null),O=I.useRef(null),[ve,Ce]=I.useState(0),fe=Array.isArray(n)?n:[n],de=ce==="transparent"?null:De(ce),me={colors:fe,paletteKey:fe.join(","),intensity:Math.max(0,d),force:Math.max(0,l),radius:y(v,4,600),persistence:y(s,0,1),viscosity:y(m,0,1),resolution:y(M,.05,1),walls:P,clickBurst:R,autoDemo:Z,autoSpeed:Math.max(0,S),autoIntensity:Math.max(0,Q),autoResumeDelay:Math.max(0,Le),intro:Fe,light:Ve,background:de?[...de,1]:[0,0,0,0],maxRatio:y(Xe,.5,3)},D=I.useRef(me);return D.current=me,I.useEffect(()=>{var _;(_=O.current)==null||_.call(O)}),I.useEffect(()=>{const _=ue.current,g=le.current;if(!_||!g)return;const e=g.getContext("webgl2",{alpha:!0,premultipliedAlpha:!0,antialias:!1,depth:!1,stencil:!1,powerPreference:"high-performance"});if(!e||!e.getExtension("EXT_color_buffer_float")&&!e.getExtension("EXT_color_buffer_half_float"))return;const w={splat:C(e,$e),smooth:C(e,Ye),divergence:C(e,He),pressure:C(e,Ke),project:C(e,qe),advect:C(e,je),display:C(e,Je)};if(Object.values(w).some(t=>!t))return;const $=window.matchMedia("(prefers-reduced-motion: reduce)").matches,he=e.createVertexArray(),h={width:1,height:1},E={width:0,height:0},c={},a={x:0,y:0,fromX:0,fromY:0,dx:0,dy:0,inside:!1,ready:!1,moved:!1,last:0},x={phase:Math.random()*100,x:0,y:0,ready:!1,ramp:0},b={active:D.current.intro&&!$&&ve===0,time:0},Y=[],G=[];let k=1,H=-1/0,L=0,K=0,ee=!0,te=document.visibilityState!=="hidden",re=!1,xe="";const q=e.createTexture();e.bindTexture(e.TEXTURE_2D,q),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_MIN_FILTER,e.LINEAR),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_MAG_FILTER,e.LINEAR),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_WRAP_S,e.CLAMP_TO_EDGE),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_WRAP_T,e.CLAMP_TO_EDGE);const Ne=t=>{t.paletteKey!==xe&&(xe=t.paletteKey,e.bindTexture(e.TEXTURE_2D,q),e.texImage2D(e.TEXTURE_2D,0,e.RGBA8,J,1,0,e.RGBA,e.UNSIGNED_BYTE,Qe(t.colors)))},W=(t,r,o)=>{const u=e.createTexture();e.bindTexture(e.TEXTURE_2D,u),e.texImage2D(e.TEXTURE_2D,0,o===2?e.RG16F:e.R16F,t,r,0,o===2?e.RG:e.RED,e.HALF_FLOAT,null),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_MIN_FILTER,e.LINEAR),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_MAG_FILTER,e.LINEAR),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_WRAP_S,e.CLAMP_TO_EDGE),e.texParameteri(e.TEXTURE_2D,e.TEXTURE_WRAP_T,e.CLAMP_TO_EDGE);const i=e.createFramebuffer();return e.bindFramebuffer(e.FRAMEBUFFER,i),e.framebufferTexture2D(e.FRAMEBUFFER,e.COLOR_ATTACHMENT0,e.TEXTURE_2D,u,0),e.clearColor(0,0,0,0),e.clear(e.COLOR_BUFFER_BIT),{texture:u,framebuffer:i}},pe=()=>{Object.keys(c).forEach(t=>{e.deleteTexture(c[t].texture),e.deleteFramebuffer(c[t].framebuffer),delete c[t]})},oe=()=>{const t=D.current,r=_.getBoundingClientRect(),o=Math.max(1,r.width),u=Math.max(1,r.height),i=Math.min(window.devicePixelRatio||1,t.maxRatio,Math.sqrt(Oe/(o*u))),f=Math.max(1,Math.round(o*i)),A=Math.max(1,Math.round(u*i));(g.width!==f||g.height!==A)&&(g.width=f,g.height=A),h.width=o,h.height=u;const p=t.resolution*.5,B=p*Math.min(1,Math.sqrt(Ge/(o*u*p*p))),U=y(Math.round(o*B),16,1024),X=y(Math.round(u*B),16,1024);U===E.width&&X===E.height||(pe(),E.width=U,E.height=X,c.velocity=W(U,X,2),c.velocityNext=W(U,X,2),c.divergence=W(U,X,1),c.pressure=W(U,X,1),c.pressureNext=W(U,X,1),e.bindFramebuffer(e.FRAMEBUFFER,null))},z=(t,r)=>{const o=c[t];c[t]=c[r],c[r]=o},T=(t,r,o)=>{e.activeTexture(e.TEXTURE0+t),e.bindTexture(e.TEXTURE_2D,r),e.uniform1i(o,t)},F=(t,r,o)=>{e.useProgram(t.program),e.uniform2f(t.uniforms.uTexel,1/E.width,1/E.height),o(t.uniforms),e.bindFramebuffer(e.FRAMEBUFFER,r?r.framebuffer:null),e.drawArrays(e.TRIANGLES,0,3)},ke=(t,r)=>{e.viewport(0,0,E.width,E.height);const o=t.walls?1:0;Y.forEach(i=>{F(w.splat,c.velocityNext,f=>{T(0,c.velocity.texture,f.uVelocity),e.uniform2f(f.uSize,h.width,h.height),e.uniform4f(f.uSegment,i.ax,i.ay,i.bx,i.by),e.uniform2f(f.uForce,i.vx,i.vy),e.uniform1f(f.uRadius,i.radius)}),z("velocity","velocityNext")}),Y.length=0;const u=Math.ceil(t.viscosity*4);for(let i=0;i<u;i++)F(w.smooth,c.velocityNext,f=>{T(0,c.velocity.texture,f.uVelocity),e.uniform1f(f.uAmount,Math.min(1,t.viscosity*4-i)*.9)}),z("velocity","velocityNext");F(w.divergence,c.divergence,i=>{T(0,c.velocity.texture,i.uVelocity),e.uniform1f(i.uWalls,o)});for(let i=0;i<We;i++)F(w.pressure,c.pressureNext,f=>{T(0,c.pressure.texture,f.uPressure),T(1,c.divergence.texture,f.uDivergence),e.uniform1f(f.uWalls,o)}),z("pressure","pressureNext");F(w.project,c.velocityNext,i=>{T(0,c.pressure.texture,i.uPressure),T(1,c.velocity.texture,i.uVelocity),e.uniform1f(i.uWalls,o)}),z("velocity","velocityNext"),F(w.advect,c.velocityNext,i=>{T(0,c.velocity.texture,i.uVelocity),e.uniform2f(i.uStep,r/h.width,r/h.height),e.uniform1f(i.uKeep,_e(t.persistence,r)),e.uniform1f(i.uWalls,o)}),z("velocity","velocityNext")},ne=t=>{e.viewport(0,0,g.width,g.height),F(w.display,null,r=>{T(0,c.velocity.texture,r.uVelocity),T(1,q,r.uPalette),e.uniform2f(r.uSimSize,E.width,E.height),e.uniform1f(r.uSpeedScale,1.25*t.intensity/(.84*Math.max(h.width,h.height))),e.uniform1f(r.uLight,t.light?1:0),e.uniform4f(r.uBackground,t.background[0],t.background[1],t.background[2],t.background[3])})},Be=t=>{const r=.62*Math.sin(t*.83+1.3)+.38*Math.sin(t*1.71+.4),o=.6*Math.sin(t*1.07+2.1)+.4*Math.cos(t*.61+.7);return[(.5+.41*r)*h.width,(.5+.41*o)*h.height]},ye=t=>{const r=y(t/1.4,0,1),o=1-Math.pow(1-r,3),u=-.6+o*3.6,i=.08+.3*o;return[(.5+Math.cos(u)*i*1.1)*h.width,(.5+Math.sin(u)*i*(h.width/h.height)*.75)*h.height]},ie=(t,r,o,u,i)=>{Y.push({ax:t[0],ay:t[1],bx:r[0],by:r[1],vx:y((r[0]-t[0])/o*u,-6e3,6e3),vy:y((r[1]-t[1])/o*u,-6e3,6e3),radius:i})},ge=t=>{if(L=0,re)return;const r=D.current;Ne(r);const o=y((t-K)/1e3||0,0,1/30);K=Math.max(K,t);const u=Math.min(1,o*60),i=t-H>r.autoResumeDelay&&!a.inside;if(b.active&&o>0){const p=ye(b.time);b.time+=o;const B=Math.sin(Math.min(1,b.time/1.4)*Math.PI);ie(p,ye(b.time),o,.2*B,r.radius),k=1,b.time>1.4&&(b.active=!1)}else if(r.autoDemo&&!$&&i&&o>0){x.ramp=Math.min(1,x.ramp+o/.8),x.phase+=o*r.autoSpeed*1.6;const p=Be(x.phase);if(x.ready){const B=.3+.7*Math.pow(.5+.5*Math.sin(x.phase*2.6),2),U=r.force/20*.45*r.autoIntensity*x.ramp*x.ramp*B*u;ie([x.x,x.y],p,o,U,r.radius)}x.x=p[0],x.y=p[1],x.ready=!0,k=1}else x.ramp=0;if(a.moved&&o>0){a.moved=!1;const p=Math.max(t-a.last,8)/1e3;ie([a.fromX,a.fromY],[a.x,a.y],p,r.force/20*.15*u,r.radius),a.fromX=a.x,a.fromY=a.y,a.last=t,k=1}const f=G.shift();f&&Y.push(f),e.bindVertexArray(he),e.disable(e.BLEND),o>0&&ke(r,o),ne(r),k*=_e(r.persistence,o),(b.active||r.autoDemo&&!$||k>.002||G.length>0||a.moved)&&ee&&te&&(L=requestAnimationFrame(ge))},V=()=>{L||re||!ee||!te||(K=performance.now(),L=requestAnimationFrame(ge))};O.current=()=>{oe(),ne(D.current),V()};const Ee=t=>{const r=_.getBoundingClientRect();return{x:t.clientX-r.left,y:r.bottom-t.clientY,inside:t.clientX>=r.left&&t.clientX<=r.right&&t.clientY>=r.top&&t.clientY<=r.bottom}},Te=t=>{const r=Ee(t);if(a.inside=r.inside,!r.inside){a.ready=!1;return}H=performance.now(),b.active=!1,a.ready||(a.fromX=r.x,a.fromY=r.y,a.x=r.x,a.y=r.y,a.last=H,a.ready=!0);const o=r.x-a.x,u=r.y-a.y;Math.hypot(o,u)>.5&&(a.dx=a.dx*.7+o*.3,a.dy=a.dy*.7+u*.3),a.x=r.x,a.y=r.y,a.moved=!0,V()},Re=t=>{const r=D.current,o=Ee(t);if(!o.inside||(H=performance.now(),!r.clickBurst||$))return;const u=Math.hypot(a.dx,a.dy)>1?Math.atan2(a.dy,a.dx):Math.random()*Math.PI*2,i=Math.cos(u),f=Math.sin(u),A=r.radius*.5,p=1400*(r.force/20);G.push({ax:o.x-i*A,ay:o.y-f*A,bx:o.x+i*A,by:o.y+f*A,vx:i*p,vy:f*p,radius:r.radius*.9}),G.length>4&&G.shift(),k=1,V()},ae=()=>{a.inside=!1,a.ready=!1},j=t=>{t.pointerType!=="mouse"&&ae()};window.addEventListener("pointermove",Te,{passive:!0}),window.addEventListener("pointerdown",Re,{passive:!0}),window.addEventListener("pointerup",j,{passive:!0}),window.addEventListener("pointercancel",j,{passive:!0}),document.documentElement.addEventListener("pointerleave",ae);const we=t=>{t.preventDefault(),re=!0,cancelAnimationFrame(L),L=0},be=()=>Ce(t=>t+1);g.addEventListener("webglcontextlost",we),g.addEventListener("webglcontextrestored",be);const Ue=new ResizeObserver(()=>{oe(),ne(D.current),V()});Ue.observe(_);const Me=new IntersectionObserver(t=>{ee=t.some(r=>r.isIntersecting),V()});Me.observe(_);const Se=()=>{te=document.visibilityState!=="hidden",V()};return document.addEventListener("visibilitychange",Se),oe(),V(),()=>{cancelAnimationFrame(L),O.current=null,Ue.disconnect(),Me.disconnect(),window.removeEventListener("pointermove",Te),window.removeEventListener("pointerdown",Re),window.removeEventListener("pointerup",j),window.removeEventListener("pointercancel",j),document.documentElement.removeEventListener("pointerleave",ae),document.removeEventListener("visibilitychange",Se),g.removeEventListener("webglcontextlost",we),g.removeEventListener("webglcontextrestored",be),e.isContextLost()||(pe(),e.deleteTexture(q),e.deleteVertexArray(he),Object.values(w).forEach(t=>e.deleteProgram(t.program)))}},[ve]),React.createElement("div",{ref:ue,className:`liquid-ether${se?` ${se}`:""}`,style:Ie},React.createElement("canvas",{ref:le,className:"liquid-ether__canvas","aria-hidden":"true"}))};export{tt as default};
