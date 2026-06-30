<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyBuddyExperienceController extends Controller
{
    private function sharedData(): array
    {
        $user = Auth::user();

        return [
            'user' => $user,
            'roles' => [
                [
                    'key' => 'student',
                    'title' => 'Student',
                    'emoji' => '🚀',
                    'summary' => 'Learn through quests, mini-apps, streaks, points, and playful progress.',
                    'cta' => 'Start as a learner',
                ],
                [
                    'key' => 'parent',
                    'title' => 'Parent',
                    'emoji' => '💜',
                    'summary' => 'Understand your child’s learning rhythm, safety, goals, and progress.',
                    'cta' => 'Guide my learner',
                ],
                [
                    'key' => 'teacher',
                    'title' => 'Teacher',
                    'emoji' => '🎓',
                    'summary' => 'Use quests, activities, and class-friendly mini-app ideas to support lessons.',
                    'cta' => 'Plan a class mission',
                ],
                [
                    'key' => 'independent_learner',
                    'title' => 'Independent Learner',
                    'emoji' => '🌙',
                    'summary' => 'Build your own study routine, track quests, and learn at your own pace.',
                    'cta' => 'Build my routine',
                ],
            ],
            'miniApps' => [
                ['name' => 'Math Quest', 'type' => 'Practice Game', 'status' => 'Planned', 'points' => 120, 'focus' => 'problem solving'],
                ['name' => 'Spelling Sprint', 'type' => 'Speed Challenge', 'status' => 'Planned', 'points' => 90, 'focus' => 'vocabulary'],
                ['name' => 'Reading Garden', 'type' => 'Reading Tracker', 'status' => 'Planned', 'points' => 110, 'focus' => 'comprehension'],
                ['name' => 'Focus Forest', 'type' => 'Focus Timer', 'status' => 'Planned', 'points' => 75, 'focus' => 'attention'],
                ['name' => 'Planner City', 'type' => 'Study Planner', 'status' => 'Planned', 'points' => 80, 'focus' => 'routine'],
                ['name' => 'Quiz Galaxy', 'type' => 'Review Mode', 'status' => 'Planned', 'points' => 100, 'focus' => 'revision'],
                ['name' => 'Shapes Lab', 'type' => 'Visual Lab', 'status' => 'Planned', 'points' => 85, 'focus' => 'geometry'],
                ['name' => 'Flashcard Castle', 'type' => 'Memory Tool', 'status' => 'Planned', 'points' => 95, 'focus' => 'recall'],
            ],
            'themes' => [
                'cosmic-dolphin' => 'Cosmic Dolphin',
                'bts-purple-galaxy' => 'BTS Purple Galaxy',
                'ocean-focus' => 'Ocean Focus',
                'candy-pop' => 'Candy Pop',
                'forest-calm' => 'Forest Calm',
                'night-study' => 'Night Study',
                'solar-gold' => 'Solar Gold',
                'neon-gamer' => 'Neon Gamer',
            ],
            'faqs' => [
                ['q' => 'Can StudyBuddy connect multiple mini-apps later?', 'a' => 'Yes. The platform is being shaped as one dashboard that can connect separate apps, games, rewards, and learner progress.'],
                ['q' => 'Do web-play and downloads exist yet?', 'a' => 'The current phase prepares the content and ecosystem UI. Real app hosting, web-play builds, iOS, Windows, and Android download pipelines are planned for the final app distribution phase.'],
                ['q' => 'Will points be shared across apps?', 'a' => 'That is the intended direction. The dashboard and Quest Vault prepare the foundation for shared points, quests, streaks, and progress.'],
                ['q' => 'Is the Professional role included?', 'a' => 'No. Current supported paths are Student, Parent, Teacher, and Independent Learner.'],
            ],
        ];
    }

    public function learningHub()
    {
        return view('studybuddy.experience.learning-hub', $this->sharedData());
    }

    public function learningPaths()
    {
        return view('studybuddy.experience.learning-paths', $this->sharedData());
    }

    public function rewards()
    {
        return view('studybuddy.experience.rewards', $this->sharedData());
    }

    public function parentsCenter()
    {
        return view('studybuddy.experience.parents-center', $this->sharedData());
    }

    public function teacherStudio()
    {
        return view('studybuddy.experience.teacher-studio', $this->sharedData());
    }

    public function safetySupport()
    {
        return view('studybuddy.experience.safety-support', $this->sharedData());
    }

    public function appEcosystem()
    {
        return view('studybuddy.experience.app-ecosystem', $this->sharedData());
    }
}
