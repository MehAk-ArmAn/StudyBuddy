@extends('layouts.app')

@section('title', 'Play Math Quest')

@section('content')
<section class="section-pad play-shell">
    <div class="game-panel">
        <p class="eyebrow">Math Quest playable route</p>
        <h1>Asteroid equation incoming!</h1>
        <p class="equation">8 × 7 = <span>?</span></p>
        <div class="answer-grid">
            <button>54</button><button>56</button><button>64</button><button>72</button>
        </div>
        <p class="hint">Prototype only: backend scoring and adaptive questions come next.</p>
    </div>
    @include('partials.mascot', ['title' => 'Buddy says: trust your orbit!'])
</section>
@endsection
