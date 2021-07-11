<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hashtag;

class HashtagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Hashtag::truncate();
        $hashtags = [
            //【時間】
            [
                'parent_hashtag' => '【作成者】',
                'hashtag' => '#個人',
            ],
            [
                'parent_hashtag' => '【作成者】',
                'hashtag' => '#法人',
            ],
            [
                'parent_hashtag' => '【作成者】',
                'hashtag' => '#無所属',
            ],
            [
                'parent_hashtag' => '【作成者】',
                'hashtag' => '#グループ',
            ],
            [
                'parent_hashtag' => '【作成者】',
                'hashtag' => '#コラボ',
            ],

            //2
            [
                'parent_hashtag' => '【時間】',
                'hashtag' => '#30分以下の配信',
            ],
            [
                'parent_hashtag' => '【時間】',
                'hashtag' => '#30～60分の配信',
            ],
            [
                'parent_hashtag' => '【時間】',
                'hashtag' => '#60分以上の配信',
            ],

            //【料金】
            [
                'parent_hashtag' => '【料金】',
                'hashtag' => '#チケット料金安め',
            ],
            [
                'parent_hashtag' => '【料金】',
                'hashtag' => '#チケット料金高め',
            ],

            //【構成】
            [
                'parent_hashtag' => '【構成】',
                'hashtag' => '#チケット料金安め',
            ],
            [
                'parent_hashtag' => '【構成】',
                'hashtag' => '#少人数',
            ],
            [
                'parent_hashtag' => '【構成】',
                'hashtag' => '#多人数',
            ],

            //【広告】
            [
                'parent_hashtag' => '【広告】',
                'hashtag' => '#スポンサー募集',
            ],
            [
                'parent_hashtag' => '【広告】',
                'hashtag' => '#広告スペース販売',
            ],

            //【SNS】
            [
                'parent_hashtag' => '【SNS】',
                'hashtag' => '#Youtube',
            ],
            [
                'parent_hashtag' => '【SNS】',
                'hashtag' => '#Twitter',
            ],
            [
                'parent_hashtag' => '【SNS】',
                'hashtag' => '#ｲﾝｽﾀｸﾞﾗﾑ',
            ],
            [
                'parent_hashtag' => '【SNS】',
                'hashtag' => '#TikTok',
            ],

            //【活動】
            [
                'parent_hashtag' => '【活動】',
                'hashtag' => '#ｲﾝﾌﾙｴﾝｻｰ',
            ],
            [
                'parent_hashtag' => '【活動】',
                'hashtag' => '#ｱｲﾄﾞﾙ',
            ],
            [
                'parent_hashtag' => '【活動】',
                'hashtag' => '#ｺｽﾌﾟﾚｲﾔｰ',
            ],
            [
                'parent_hashtag' => '【活動】',
                'hashtag' => '#芸人',
            ],
            [
                'parent_hashtag' => '【活動】',
                'hashtag' => '#声優',
            ],
            [
                'parent_hashtag' => '【活動】',
                'hashtag' => '#ﾐｭｰｼﾞｼｬﾝ',
            ],
            [
                'parent_hashtag' => '【活動】',
                'hashtag' => '#実況者',
            ],
            [
                'parent_hashtag' => '【活動】',
                'hashtag' => '#手品師',
            ],
            [
                'parent_hashtag' => '【活動】',
                'hashtag' => '#占い師',
            ],
            [
                'parent_hashtag' => '【活動】',
                'hashtag' => '#医師',
            ],
            [
                'parent_hashtag' => '【活動】',
                'hashtag' => '#資格保持者',
            ],
            [
                'parent_hashtag' => '【活動】',
                'hashtag' => '#講師',
            ],
            [
                'parent_hashtag' => '【活動】',
                'hashtag' => '#家庭教師',
            ],

            //【配信形式】
            [
                'parent_hashtag' => '【配信形式】',
                'hashtag' => '#コンサート',
            ],
            [
                'parent_hashtag' => '【配信形式】',
                'hashtag' => '#ライブ中継',
            ],
            [
                'parent_hashtag' => '【配信形式】',
                'hashtag' => '#演奏会',
            ],
            [
                'parent_hashtag' => '【配信形式】',
                'hashtag' => '#講演会',
            ],

            //【内容】
            [
                'parent_hashtag' => '【内容】',
                'hashtag' => '#教えます',
            ],
            [
                'parent_hashtag' => '【内容】',
                'hashtag' => '#披露します',
            ],
            [
                'parent_hashtag' => '【内容】',
                'hashtag' => '#チャレンジします',
            ],

            //【その他】
            [
                'parent_hashtag' => '【その他】',
                'hashtag' => '#一緒に楽しみたい',
            ],
            [
                'parent_hashtag' => '【その他】',
                'hashtag' => '#一緒に学びたい',
            ],
            [
                'parent_hashtag' => '【その他】',
                'hashtag' => '#一緒に頑張りたい',
            ],

        ];
        foreach ($hashtags as $hashtag) {
            Hashtag::create($hashtag);
        }
    }
}
