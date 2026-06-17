<?php

namespace Database\Seeders;

use App\Models\FooterItem;
use App\Models\HomepageSection;
use App\Models\HomepageSectionItem;
use App\Models\MediaAsset;
use App\Models\NavigationItem;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionItem;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private string $assetBase = 'assets/studybuddy-imgs/';

    public function run(): void
    {
        $this->seedSettings();
        $this->seedMedia();
        $this->seedNavigation();
        $this->seedFooter();
        $this->seedHomepage();
        $this->seedPages();
    }

    private function seedSettings(): void
    {
        foreach ([
            ['site_name', 'StudyBuddy', 'text', 'identity'],
            ['brand_name', 'StudyBuddy', 'text', 'identity'],
            ['logo_path', $this->assetBase.'brand/logo-icon.png', 'image', 'identity'],
            ['favicon_path', $this->assetBase.'brand/logo-icon.png', 'image', 'identity'],
            ['seo_title', 'StudyBuddy | Learn. Play. Grow. Your Way.', 'text', 'seo'],
            ['seo_description', 'A magical cosmic learning universe for kids, families, and classrooms.', 'textarea', 'seo'],
            ['global_cta_label', 'Start Learning', 'text', 'navigation'],
            ['global_cta_url', '/apps', 'url', 'navigation'],
            ['footer_brand_text', 'StudyBuddy', 'text', 'footer'],
            ['footer_description', 'A safe, magical learning universe where kids practice, focus, read, plan, and grow with their personal StudyBuddy.', 'textarea', 'footer'],
            ['footer_legal_text', '© 2026 StudyBuddy. Learn. Play. Grow. Your Way.', 'text', 'footer'],
        ] as [$key, $value, $type, $group]) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type, 'group' => $group]);
        }
    }

    private function seedMedia(): void
    {
        foreach ([
            ['StudyBuddy Logo', 'brand/logo-icon.png'], ['Hero Dolphin Book', 'hero/hero-dolphin-book.png'],
            ['Math Quest Icon', 'apps/app-math-quest.png'], ['Spelling Sprint Icon', 'apps/app-spelling-sprint.png'],
            ['Reading Garden Icon', 'apps/app-reading-garden.png'], ['Focus Forest Icon', 'apps/app-focus-forest.png'],
            ['Planner City Icon', 'apps/app-planner-city.png'], ['Quiz Galaxy Icon', 'apps/app-quiz-galaxy.png'],
            ['Shapes Lab Icon', 'apps/app-shapes-lab.png'], ['Flashcard Castle Icon', 'apps/app-flashcard-castle.png'],
            ['Purple Planet', 'decor/planets/planet-purple-lg.png'], ['Ringed Planet', 'decor/planets/planet-ringed-lg.png'],
        ] as [$title, $path]) {
            MediaAsset::updateOrCreate(['path' => $this->assetBase.$path], ['title' => $title, 'alt_text' => $title, 'type' => 'image', 'mime_type' => 'image/png', 'is_active' => true]);
        }
    }

    private function seedNavigation(): void
    {
        NavigationItem::query()->delete();
        foreach ([['Home','/',1],['Apps','/apps',2],['Parents','/for-parents',3],['Teachers','/for-teachers',4],['About','/about-us',5],['Contact','/contact-us',6],['Support','/support',7]] as [$label,$url,$sort]) {
            NavigationItem::create(['label'=>$label,'url'=>$url,'sort_order'=>$sort,'is_enabled'=>true,'opens_new_tab'=>false]);
        }
    }

    private function seedFooter(): void
    {
        FooterItem::query()->delete();
        foreach ([['Explore','Home','/',1],['Explore','Apps','/apps',2],['Explore','Parents','/for-parents',3],['Explore','Teachers','/for-teachers',4],['Company','About Us','/about-us',1],['Company','Contact Us','/contact-us',2],['Company','Support','/support',3],['Legal','Privacy Policy','/privacy-policy',1],['Legal','Data Deletion','/data-deletion',2]] as [$group,$label,$url,$sort]) {
            FooterItem::create(['group'=>$group,'label'=>$label,'url'=>$url,'sort_order'=>$sort,'is_enabled'=>true,'opens_new_tab'=>false]);
        }
    }

    private function seedHomepage(): void
    {
        HomepageSection::query()->delete();
        $this->section('hero','hero',1,'Magical learning universe','Learn. Play. Grow. Your Way.','A bright cosmic learning world where kids practice, focus, read, plan, and grow through playful mini experiences.','Built for curious kids, confident parents, and classroom-friendly routines.',$this->assetBase.'hero/hero-dolphin-book.png','See what we do','#what-we-do','Explore apps','/apps',['highlight'=>'Your Way','bubble_one'=>'Daily wins ✨','bubble_two'=>'Focus mode 🌙','bubble_three'=>'Tiny quests 🚀']);
        $what=$this->section('what_we_do','what',2,'What we do','Turn study time into a tiny adventure','StudyBuddy blends mini apps, calming routines, parent clarity, and teacher-friendly structure into one magical learning identity.',null,null);
        foreach ([['practice','Practice','Small daily quests keep skills fresh.','✏️','/apps',1],['focus','Focus','Calm screens help kids stay steady.','🌙','/apps',2],['growth','Grow','Clear wins make progress feel exciting.','🌱','/for-parents',3],['support','Support','Parents and teachers get simple guidance.','💬','/support',4]] as [$key,$title,$subtitle,$icon,$url,$sort]) $this->item($what,$key,$title,$subtitle,null,null,$icon,'Open',$url,$sort);
        $apps=$this->section('apps_preview','apps-preview',3,'Mini apps preview','Peek at the learning universe','The full app library lives on its own page. Here is a quick sparkle preview.',null,null,'Open apps page','/apps');
        foreach (array_slice($this->appData(),0,4) as $row) { [$key,$title,$subtitle,$body,$image,$badge,$sort]=$row; $this->item($apps,$key,$title,$subtitle,$body,$this->assetBase.$image,$badge,null,'/apps',$sort); }
        $paths=$this->section('page_paths','paths',4,'Choose your path','One homepage, many clear pages','Each part of the product story now has its own editable CMS page.',null,null);
        foreach ([['apps','Apps','Explore all StudyBuddy mini apps.','🚀',$this->assetBase.'apps/app-quiz-galaxy.png','/apps',1],['parents','Parents','Understand routines, trust, and growth.','💜',$this->assetBase.'decor/planets/planet-ringed-lg.png','/for-parents',2],['teachers','Teachers','See classroom-friendly learning energy.','🎓',$this->assetBase.'apps/app-planner-city.png','/for-teachers',3],['support','Support','Get help, contact, and safety info.','✨',$this->assetBase.'brand/logo-icon.png','/support',4]] as [$key,$title,$subtitle,$badge,$image,$url,$sort]) $this->item($paths,$key,$title,$subtitle,null,$image,$badge,'Open',$url,$sort);
        $why=$this->section('why','features',5,'Why it feels different','Soft structure, shiny motivation, real progress','StudyBuddy keeps learning playful without losing focus.',null,null);
        foreach ([['playful','Playful Practice','Lessons feel light and doable.','Kids practice without overwhelm.','⭐',1],['calm','Calm Focus','Gentle routines make study less stressful.','Focus tools support attention.','🌿',2],['reading','Reading Growth','Stories build confidence.','A cozy place to grow vocabulary.','📚',3],['parent','Parent Confidence','Parents understand progress quickly.','Clear sections support learning.','💜',4],['classroom','Classroom Ready','Polished learning identity for school use.','Teachers guide learners with structure.','🎓',5],['reward','Reward Motivation','Stars and badges keep goals exciting.','Progress feels earned and fun.','🏆',6]] as [$key,$title,$subtitle,$body,$badge,$sort]) $this->item($why,$key,$title,$subtitle,$body,null,$badge,null,null,$sort);
        $this->section('cta','cta',6,'Ready to glow up study time?','Start your cosmic learning journey','Every page, card, button, image path, and message is controlled from the admin CMS.','Update the text, swap images, reorder sections, and keep the StudyBuddy world alive.',$this->assetBase.'hero/hero-dolphin-book.png','Contact us','/contact-us','Get support','/support');
    }

    private function seedPages(): void
    {
        Page::query()->delete();
        $this->seedAppsPage(); $this->simplePage('for-parents','For Parents','Parents','Study time that feels calmer at home','A parent-friendly learning world built for routines, confidence, and gentle progress.','Everything is designed to feel safe, simple, and magical for families.',$this->assetBase.'decor/planets/planet-ringed-lg.png',2,[['Daily Routine','Turn practice into a tiny habit.','Short sessions make learning easier to repeat.','🌙'],['Clear Confidence','Know what StudyBuddy is helping with.','Parents understand the product promise quickly.','💜'],['Less Chaos','A soft visual universe, not noisy screens.','The look feels playful while staying gentle.','✨']]);
        $this->simplePage('for-teachers','For Teachers','Teachers','Classroom-friendly learning energy','A polished learning identity for practice, review, calm focus, and growth.','Teachers get a clean story and flexible page content.',$this->assetBase.'apps/app-planner-city.png',3,[['Practice Blocks','Use mini apps for review moments.','StudyBuddy can support subject practice and quick wins.','✏️'],['Focus Support','Calm visuals support attention.','The product tone helps reduce overwhelm.','🌿'],['Planning Energy','A system that can grow into class routines.','Planner City and progress ideas show classroom potential.','🏙️']]);
        $this->simplePage('about-us','About Us','About','A magical study world with a simple mission','StudyBuddy exists to make learning feel playful, safe, calm, and rewarding.','The brand blends cosmic visuals, soft motivation, and kid-friendly structure.',$this->assetBase.'hero/hero-dolphin-book.png',4,[['Learning can be playful','Kids deserve bright, friendly practice.','The experience should feel like a little adventure.','🚀'],['Learning can be calm','Not every screen needs to be loud.','StudyBuddy uses a softer cosmic world.','🌙'],['Learning can grow','Small wins can build big confidence.','The platform can expand through the CMS and app world.','🌱']]);
        $this->legalPage('privacy-policy','Privacy Policy','Privacy','Privacy Policy','A simple privacy page for StudyBuddy visitors and families.','Replace this starter content with your final legal wording before public launch.',5,[['Information we may collect','We may collect account details, contact messages, usage preferences, and technical information needed to run the website.'],['How information is used','Information may be used to provide the website, improve the experience, respond to support requests, and keep the service safe.'],['Privacy questions','For privacy questions, contact the StudyBuddy team through the contact page.']]);
        $this->legalPage('data-deletion','Data Deletion','Data Deletion','Data Deletion Request','A clear page for users who want their StudyBuddy data deleted.','This page gives a basic editable deletion request process.',6,[['Step 1','Send a request through the contact page using the email linked to your account.'],['Step 2','Tell us whether you want account deletion, message deletion, or full personal data deletion.'],['Step 3','We will review and process the request according to the final StudyBuddy policy.']]);
        $this->simplePage('contact-us','Contact Us','Contact','Contact the StudyBuddy team','Questions, partnerships, schools, parent feedback, and support can start here.','This is a starter contact page. Update the text, email, and support details in admin.',$this->assetBase.'apps/app-flashcard-castle.png',7,[['General Questions','For product or website questions.','Add your official email or form link here.','💌'],['Schools & Teachers','For classroom or school interest.','Use this card for education partnerships.','🎓'],['Parents & Families','For family questions and safety notes.','Guide parents to support or contact channels.','💜']]);
        $this->simplePage('support','Support','Support','StudyBuddy Support','A friendly help page for families, teachers, and visitors.','Use admin to edit every help message, image, and support card.',$this->assetBase.'apps/app-focus-forest.png',8,[['Getting Started','Find your way around StudyBuddy.','Start with Apps, Parents, and Teachers pages.','🚀'],['Account Help','Need login or account help?','Add account instructions and contact details here.','🔐'],['Safety & Privacy','Learn how privacy and deletion requests work.','Link users to Privacy Policy and Data Deletion pages.','🛡️']]);
    }

    private function seedAppsPage(): void
    {
        $page=$this->page('apps','Apps','Apps','Explore the mini app universe','Choose a learning world for math, spelling, reading, focus, planning, quizzes, shapes, or flashcards.','Every app card, description, image, and button is editable from admin.',$this->assetBase.'apps/app-quiz-galaxy.png','Contact us','/contact-us',1);
        $section=$this->pageSection($page,'all_apps','app-grid',1,'Mini apps','All learning worlds','A bright app-store style library for the StudyBuddy universe.',null,null);
        foreach ($this->appData() as $row) { [$key,$title,$subtitle,$body,$image,$badge,$sort]=$row; $this->pageItem($section,$key,$title,$subtitle,$body,$this->assetBase.$image,$badge,'Learn more','/contact-us',$sort); }
    }

    private function simplePage(string $slug,string $title,string $label,string $hero,string $subtitle,string $body,string $image,int $sort,array $cards): void
    { $page=$this->page($slug,$title,$label,$hero,$subtitle,$body,$image,'Contact us','/contact-us',$sort); $section=$this->pageSection($page,'main_cards','cards',1,$label,'What you can edit','Every card, title, body, icon, button, and image can be changed in admin.',null,null); foreach ($cards as $i=>$card) $this->pageItem($section,strtolower(str_replace(' ','-',$card[0])),$card[0],$card[1],$card[2],null,$card[3],null,null,$i+1); }
    private function legalPage(string $slug,string $title,string $label,string $hero,string $subtitle,string $body,int $sort,array $items): void
    { $page=$this->page($slug,$title,$label,$hero,$subtitle,$body,$this->assetBase.'brand/logo-icon.png','Contact us','/contact-us',$sort); $section=$this->pageSection($page,'legal_content','legal',1,$label,'Editable policy content',$body,null,null); foreach ($items as $i=>$item) $this->pageItem($section,'item-'.$i,$item[0],null,$item[1],null,null,null,null,$i+1); }

    private function appData(): array
    { return [['math-quest','Math Quest','Practice math in a fun way.','Numbers become magical quests with bright rewards and tiny wins.','apps/app-math-quest.png','4.8',1],['spelling-sprint','Spelling Sprint','Improve spelling and vocabulary.','Rocket through words, letters, and confidence-building practice.','apps/app-spelling-sprint.png','4.7',2],['reading-garden','Reading Garden','Read stories and build reading skills.','Grow reading habits with calm magical story moments.','apps/app-reading-garden.png','4.8',3],['focus-forest','Focus Forest','Stay focused and calm.','A peaceful space for focus and study routines.','apps/app-focus-forest.png','4.8',4],['planner-city','Planner City','Organize tasks and homework.','Turn reminders and goals into a tiny futuristic city.','apps/app-planner-city.png','4.6',5],['quiz-galaxy','Quiz Galaxy','Test knowledge and earn stars.','Explore quiz planets and collect sparkly wins.','apps/app-quiz-galaxy.png','4.7',6],['shapes-lab','Shapes Lab','Learn shapes and their world.','Play with geometry and bright shape experiments.','apps/app-shapes-lab.png','4.6',7],['flashcard-castle','Flashcard Castle','Study anywhere with flashcards.','Build memory inside a cute castle of cards.','apps/app-flashcard-castle.png','4.8',8]]; }
    private function page(string $slug,string $title,string $label,string $hero,string $subtitle,string $body,string $image,?string $button,?string $url,int $sort): Page
    { return Page::updateOrCreate(['slug'=>$slug],['template'=>'cosmic','title'=>$title,'nav_label'=>$label,'meta_title'=>$title.' | StudyBuddy','meta_description'=>$subtitle,'eyebrow'=>$label,'hero_title'=>$hero,'hero_subtitle'=>$subtitle,'hero_body'=>$body,'hero_image_path'=>$image,'button_label'=>$button,'button_url'=>$url,'secondary_button_label'=>'Back home','secondary_button_url'=>'/','sort_order'=>$sort,'is_enabled'=>true,'settings'=>['editable_from_admin'=>true]]); }
    private function pageSection(Page $page,string $key,string $type,int $sort,?string $eyebrow,?string $title,?string $subtitle,?string $body,?string $image,?string $button): PageSection
    { return PageSection::updateOrCreate(['page_id'=>$page->id,'section_key'=>$key],['section_type'=>$type,'eyebrow'=>$eyebrow,'title'=>$title,'subtitle'=>$subtitle,'body'=>$body,'image_path'=>$image,'button_label'=>$button,'button_url'=>null,'sort_order'=>$sort,'is_enabled'=>true,'settings'=>['editable_from_admin'=>true]]); }
    private function pageItem(PageSection $section,string $key,?string $title,?string $subtitle,?string $body,?string $image,?string $badge,?string $button,?string $url,int $sort): void
    { PageSectionItem::updateOrCreate(['page_section_id'=>$section->id,'item_key'=>$key],['title'=>$title,'subtitle'=>$subtitle,'body'=>$body,'image_path'=>$image,'badge_text'=>$badge,'button_label'=>$button,'button_url'=>$url,'sort_order'=>$sort,'is_enabled'=>true,'settings'=>['editable_from_admin'=>true]]); }
    private function section(string $key,string $type,int $sort,?string $eyebrow,?string $title,?string $subtitle,?string $body,?string $image,?string $button=null,?string $url=null,?string $second=null,?string $secondUrl=null,?array $settings=null): HomepageSection
    { return HomepageSection::updateOrCreate(['section_key'=>$key],['section_type'=>$type,'eyebrow'=>$eyebrow,'title'=>$title,'subtitle'=>$subtitle,'body'=>$body,'image_path'=>$image,'button_label'=>$button,'button_url'=>$url,'secondary_button_label'=>$second,'secondary_button_url'=>$secondUrl,'sort_order'=>$sort,'is_enabled'=>true,'settings'=>$settings]); }
    private function item(HomepageSection $section,string $key,?string $title,?string $subtitle,?string $body,?string $image,?string $badge,?string $button,?string $url,int $sort): void
    { HomepageSectionItem::updateOrCreate(['homepage_section_id'=>$section->id,'item_key'=>$key],['title'=>$title,'subtitle'=>$subtitle,'body'=>$body,'image_path'=>$image,'badge_text'=>$badge,'button_label'=>$button,'button_url'=>$url,'sort_order'=>$sort,'is_enabled'=>true]); }
}
