{{-- W25: The exact saved Base44 homepage remains the content source.
     Its former static header is swapped for the SAME Laravel navigation
     partial used by registration, apps, dashboards and family pages. --}}
@php
    $sb25File = public_path('assets/studybuddy-base44-exact/home.html');
    $sb25Html = is_file($sb25File) ? file_get_contents($sb25File) : '<!doctype html><html><body><main><h1>StudyBuddy website assets are missing.</h1></main></body></html>';
    // Remove legacy Gooey scripts/styles: one W25 React renderer owns navigation.
    $sb25Html = preg_replace('~<script\b[^>]*\bsrc=["\'][^"\']*studybuddy-gooey\.js[^"\']*["\'][^>]*>\s*</script>~i', '', $sb25Html);
    $sb25Html = preg_replace('~<link\b[^>]*\bhref=["\'][^"\']*studybuddy-gooey\.css[^"\']*["\'][^>]*>~i', '', $sb25Html);
    $sb25SharedHeader = view('partials.studybuddy-w25-header')->render();
    $sb25Html = preg_replace('~<header\b[^>]*>.*?</header>~is', $sb25SharedHeader, $sb25Html, 1);
    $sb25Css = asset('assets/studybuddy-w25/shell.css').'?v='.filemtime(public_path('assets/studybuddy-w25/shell.css'));
    $sb25GooeyCss = asset('assets/studybuddy-w25/gooey.css').'?v='.filemtime(public_path('assets/studybuddy-w25/gooey.css'));
    $sb25Javascript = asset('assets/studybuddy-w25/shell.js').'?v='.filemtime(public_path('assets/studybuddy-w25/shell.js'));
    $sb25React = asset('assets/studybuddy-w25/gooey.js').'?v='.filemtime(public_path('assets/studybuddy-w25/gooey.js'));
    $sb25Head = '<link rel="stylesheet" href="'.e($sb25GooeyCss).'">'.'<link rel="stylesheet" href="'.e($sb25Css).'">';
    $sb25Foot = '<script src="'.e($sb25Javascript).'" defer></script><script type="module" src="'.e($sb25React).'"></script>';
    $sb25Html = str_replace('</head>', $sb25Head.'</head>', $sb25Html);
    $sb25Html = str_replace('</body>', $sb25Foot.'</body>', $sb25Html);
@endphp
{!! $sb25Html !!}
