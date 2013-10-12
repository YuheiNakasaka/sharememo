# Share Memo

[http://www.sharememo.net/](http://www.sharememo.net/)

[blog](http://razokulover.hateblo.jp/entry/2013/10/12/220505)

## 概要

Share Memoは勉強会等で書いたメモをすぐさまシェアするためのサービスでした。

しかし、ユーザー数が思うようにのびなかったことや個人的に開発している別案件があり、
メンテナンスするのが困難になったためShareMemoを閉鎖することにしました。

現在は登録していたユーザー分の過去のメモはみえる状態にしているので、今すぐにメモが全て消え去ってしまうことはありません。

こういうわけでShareMemoを閉鎖することになりましたが、ただ閉鎖するのはもったいないのでパスワードや半端なテストなどを削除した形でソースを公開することにします。

ShareMemoは現在人気急上昇中のPHPフレームワークのLaravel4を用いて作られているので少しでも参考になればと思います。

一応、CRUD系の機能は一通り揃えているのでフレームワーク初心者の人でもcloneして少しいじれば遊べると思います。

また、Laravel4にTwitter・facebook認証のログイン機能を追加する方法やmarkdownのリアルタイムプレビューも汚いながら公開しているので参考になるかもしれません。

注意としては、もしかするとセキュリティホールがあるかもしれないので気を付けてください。

そのままこのソースを利用してサービスを作り、攻撃を受けても責任はとれません。

## 構成

- 言語:PHP,Javascript
- フレームワーク:Laravel4
- テンプレートエンジン:Smarty
- Markdown Library:markd ([https://github.com/chjj/marked](https://github.com/chjj/marked))
- 静的ファイル:S3
- デザイン:Twitter Bootstrap3(フラットデザイン)

## 要件

- PHP >= 5.3.7
- Mcrypt PHP Extension
- composer

## インストール

```
$git clone
$cd sharememo
$chmod -R 777 app/storage storage
$composer install
```

- app/config/facebook.phpとapp/config/packages/philo/twitter/config.phpにIDとkeyを追加してください。
- app/config/database.phpにDB設定をしてください

###マイグレーションの実行

```
$php artisan migrate
```

- 独自で設定したURLへアクセス

## License

Copyright (c) 2013, Yuhei Nakasaka. (MIT License)

See LICENSE.txt for more info.
