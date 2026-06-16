<?php

namespace Database\Seeders;

use App\Models\FooterItem;
use App\Models\HomepageSection;
use App\Models\MediaAsset;
use App\Models\NavigationItem;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email'=>'admin@studybuddy.local'], ['name'=>'StudyBuddy Admin','password'=>Hash::make('ChangeMe12345!'),'role'=>'admin','is_admin'=>true]);
        collect([
            ['site_name','StudyBuddy','text','identity'],['brand_name','StudyBuddy','text','identity'],['logo_path','assets/studybuddy/logo-icon.png','image','identity'],['favicon_path','assets/studybuddy/logo-icon.png','image','identity'],['seo_title','StudyBuddy | Cosmic Learning for Curious Kids','text','seo'],['seo_description','A premium learning homepage for families and classrooms, fully managed from the StudyBuddy admin CMS.','textarea','seo'],['seo_keywords','kids learning, study planner, family learning, classroom support','text','seo'],['homepage_meta_image','assets/studybuddy/hero-dolphin-book.png','image','seo'],['footer_brand_text','StudyBuddy','text','footer'],['footer_description','Cosmic calm, playful structure, and confidence-building learning journeys for children, parents, and teachers.','textarea','footer'],['footer_legal_text','© 2026 StudyBuddy. Built for safer, brighter learning.','text','footer'],['global_cta_label','Start learning','text','cta'],['global_cta_url','#cta','url','cta'],
        ])->each(fn($s)=>SiteSetting::updateOrCreate(['key'=>$s[0]], ['value'=>$s[1],'type'=>$s[2],'group'=>$s[3]]));
        foreach (['logo-icon.png','hero-dolphin-book.png','app-math-quest.png','app-focus-forest.png','app-flashcard-castle.png','app-reading-garden.png','app-quiz-galaxy.png','planet-ringed-lg.png'] as $i=>$file) MediaAsset::create(['title'=>str($file)->replace(['.png','-'],' ')->title(),'alt_text'=>'StudyBuddy '.$file,'path'=>'assets/studybuddy/'.$file,'type'=>'image','mime_type'=>'image/png','is_active'=>true]);
        foreach ([['Benefits','#features'],['Learning cards','#learning-cards'],['Families','#parents'],['Teachers','#teachers']] as $i=>$n) NavigationItem::create(['label'=>$n[0],'url'=>$n[1],'sort_order'=>$i+1,'is_enabled'=>true]);
        foreach ([['company','Benefits','#features'],['company','Learning cards','#learning-cards'],['support','Parent safety','#parents'],['support','Classroom value','#teachers'],['legal','Privacy promise','#footer'],['social','Instagram','https://instagram.com/']] as $i=>$f) FooterItem::create(['group'=>$f[0],'label'=>$f[1],'url'=>$f[2],'sort_order'=>$i+1,'is_enabled'=>true,'opens_new_tab'=>str_starts_with($f[2],'http')]);
        $sections=[
            ['hero','hero','A brighter way to learn','Make every study session feel calm, magical, and doable.','StudyBuddy turns learning plans, practice cards, confidence boosts, and family visibility into one polished experience.','assets/studybuddy/hero-dolphin-book.png','Explore benefits','#features','See learning cards','#learning-cards'],
            ['learning-cards','cards','Featured learning cards','Choose the right learning moment','Editable cards highlight the experiences StudyBuddy offers without sending families to fake prototype pages.',null,null,null,null],
            ['features','cards','Why families love it','Structure, sparkle, and steady progress','StudyBuddy combines premium design with practical supports for real learning routines.',null,null,null,null],
            ['stats','stats','Trusted learning signals','Confidence you can see','Simple CMS-managed proof points keep the homepage credible and easy to update.',null,null,null,null],
            ['parents','split','Parent safety and calm','A learning space parents can trust','Clear routines, gentle motivation, and no noisy public portals. The homepage stays focused on value while admins control every message.','assets/studybuddy/planet-ringed-lg.png',null,null,null],
            ['teachers','split','Classroom value','Support every learner without clutter','Teachers can understand StudyBuddy’s classroom promise from a clean section edited directly in the CMS.','assets/studybuddy/app-quiz-galaxy.png',null,null,null],
            ['cta','cta','Ready for a calmer learning launch?','Build the homepage from the CMS','Edit headlines, cards, links, images, SEO, footer, and navigation from one admin area.','assets/studybuddy/sparkles-pack.png','Open admin','/admin/login',null,null],
        ];
        foreach($sections as $order=>$s){$sec=HomepageSection::create(['section_key'=>$s[0],'section_type'=>$s[1],'eyebrow'=>$s[2],'title'=>$s[3],'subtitle'=>$s[4],'body'=>$s[4],'image_path'=>$s[5],'button_label'=>$s[6],'button_url'=>$s[7],'secondary_button_label'=>$s[8],'secondary_button_url'=>$s[9],'sort_order'=>$order+1,'is_enabled'=>true]);
            if(in_array($s[0],['learning-cards','features','stats'])) foreach(range(1,3) as $i) $sec->items()->create(['title'=>['Quest practice','Focus routines','Memory boosts'][$i-1] ?? 'Learning card','subtitle'=>'CMS editable card','body'=>'A polished, database-driven card with safe links and flexible imagery.','image_path'=>['assets/studybuddy/app-math-quest.png','assets/studybuddy/app-focus-forest.png','assets/studybuddy/app-flashcard-castle.png'][$i-1],'badge_text'=>['Playful','Calm','Smart'][$i-1],'sort_order'=>$i,'is_enabled'=>true]);
        }
    }
}
