@extends('layouts.app')

@section('title', 'Safety & Support | StudyBuddy')

@section('content')
<main class="sbx-shell" data-sbx-page="safety-support">
    @include('studybuddy.experience.partials.experience-nav')

    <section class="sbx-hero sbx-reveal">
        <div>
            <span class="sbx-kicker">Help, trust, and clarity</span>
            <h1>Safety & Support Center</h1>
            <p>
                A clear support area for questions, account guidance, safety expectations,
                and future contact flows.
            </p>
        </div>
        <aside class="sbx-orbit-card">
            <span class="sbx-orbit-card__icon">🛡️</span>
            <strong>Support tone</strong>
            <p>Kind, clear, and safe for families and learners.</p>
        </aside>
    </section>

    <section class="sbx-faq sbx-reveal" data-sbx-faq>
        @foreach($faqs as $faq)
            <article>
                <button type="button" data-sbx-faq-button>{{ $faq['q'] }} <span>+</span></button>
                <p>{{ $faq['a'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="sbx-panel sbx-reveal">
        <div class="sbx-section-head">
            <span class="sbx-kicker">Support template</span>
            <h2>Copy a support message</h2>
        </div>
        <textarea data-sbx-copy-source readonly>Hello StudyBuddy team, I need help with my account or learning path. My role is: ____. The issue is: ____.</textarea>
        <button type="button" class="sbx-btn sbx-btn--primary" data-sbx-copy-template>Copy template</button>
        <p class="sbx-copy-status" data-sbx-copy-status></p>
    </section>
</main>
@endsection
