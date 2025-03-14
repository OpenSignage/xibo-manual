---
toc: "scheduling"
maxHeadingLevel: 3
minHeadingLevel: 2
excerpt: "スケジュールを簡素化するために独自の Day Parts を作成する"
keywords: "例外、営業時間の表示、事前定義"
persona: "スケジュール マネージャー"
---

# Dayparting

{tip}
放送番組では、Dayparting とは放送日をいくつかの部分に分割し、その時間帯に適した異なる種類のラジオ番組やテレビ番組を放送する手法です。
-- Wikipedia
{/tip}

[[PRODUCTNAME]] は、曜日の例外を含めることができる複数の Dayparts の作成をサポートしています。つまり、1 日を必要な数の **事前定義** 部分に分割できます。
{tip}
典型的な使用例は、朝食、昼食、夕食に異なるコンテンツを表示するホスピタリティ ユーザーです。デイパート機能を使用すると、ユーザーは朝食、昼食、夕食のデイパートを作成できます。各デイパートの開始日と終了日はそれぞれ異なるため、選択して日々のスケジュールを簡素化できます。

{/tip}

{version}
**デイパート** を作成して、[ディスプレイの営業時間](displays_settings.html#content-operating-hours) を設定することもできます。

{/version}

## デイパートの追加

デイパートは、メインの CMS メニューの **デイパート** から作成および管理されます。

- **デイパートの追加** ボタンを選択します。

- フォームのフィールドに入力して構成します。

{tip}
**例外** を追加して、選択した日に異なる開始時間と終了時間を定義します。

{/tip}

保存すると、[イベント](scheduling_events.html) を追加するときに、スケジュール フォームの **デイパート** ドロップダウン メニューでデイパートを選択できるようになります。

{tip}
以下のデイパート フォームは、朝食のデイパートの例を示しています:

![朝食のデイパートの例](img/v4_scheduling_daypart_form.png)

土曜日と日曜日は例外として設定されており、これらの日は朝食の開始時間と終了時間が異なります:

![デイパート フォームの例外タブ](img/v4_scheduling_daypart_form_exceptions.png)

スケジュール設定時に、**朝食** デイパートがドロップダウンに表示され、選択できます。選択すると、開始/終了日付の時間セレクターが日付のみのセレクターに変わり、イベントが発生する曜日に応じて、デイパート設定から時間が取得されます。
{/tip}

## デイパートの編集
行メニューを使用して、既存のデイパートを編集します。

{tip}
他のユーザー/ユーザー グループと [共有](users_features_and_sharing.html#content-share) するために、デイパートの共有オプションを追加します。

デイパートの開始/終了時間または例外を更新すると、既存の将来のイベントが新しく定義された時間で更新されます。

現在の時間を超えて繰り返し実行されるように設定されている既存の [定期的なスケジュール](scheduling_events.html#content-repeats) には、更新された情報を反映する新しいスケジュールが作成されます。
{/tip}
