EC2-Connect
=============

Ec2-Connect は AmazonWebService EC2 インスタンスへ SSH ログインするためのヘルパーツールである。  
ElasticIP のない EC2 インスタンスへのログインは常に煩わしさつきまとう。  
その煩わしさを多少軽減するツールとして利用すると良い。


## Usage
使い方だ。  
まずパラメータを何も与えずにコマンドを撃て。

```
$ EC2-Connect
0:  running  WebServiceApp  
1:  running  DatabaseApp  
2:  terminated  ScaleOutApp  
3:  running  ScaleOutApp  
4:  stopped  TestServer  
5:  terminated
```

このように現在稼働しているインスタンスが表示される。  
今回表示しているのはサンプルのインスタンスだ。  

では、WebServiceApp インスタンスにログインしてみよう。  
このようにコマンドを撃て。

```
$ EC2-Connect 0
```

引数に与えた 0 は先刻表示したインスタンス群の一番左に記載されている数字だ。  
数字で対象のインスタンスを指定することが出来る。  
このままインスタンスにログイン出来れば素晴らしいが、そこまでは出来ない。  
煩わしさを多少軽減するツールだからだ。  

おもむろに Ctrl + v を押せ。  
こうなる。

```
$ ssh ec2-user@ec2-xxx-xx-xx-xx.ap-northeast-1.compute.amazonaws.com -i /Users/hoge/.ssh/a_1.pem
```

ENTER を押せばインスタンスにログイン可能だ。  
要するに、SSH のコマンドがクリップボードに格納されている。

また、こんな指定の仕方も可能だ。

```
$ EC2-Connect WebService
$ EC2-Connect ec2-user@ec2-xxx-xx-xx-xx.ap-northeast-1.compute.amazonaws.com
```

どちらも同じでログインするための SSH コマンドがクリップボードに格納される。  
どうだろう、煩わしさが多少軽減したのではないだろうか。



## 準備
うむ。EC2-Connect を使いたくなっているのは分かっている。  
この煩わしさ多少軽減ツールを使うためには準備が必要だ。  

### Composer
EC2-Connect は内部で AwsSdkPHP2 を使っている。  
それを Composer でダウンロードしてみろ。

``` 
$ composer.phar install
```

### aws.ini
AWS の認証キーを指定しなければもちろん使えない。  
オリジナルの設定ファイルをコピーしろ。

```
$ cp config/aws.ini.orig config/aws.ini
```

ファイルを開いて設定を記載しろ。
sshkey には使用する ssh キーへのパスを指定するのだ。

```
key=xxxxxxxxxxxxxxxx
secret=xxxxxxxxxxxxxxxx
sshkey=/Users/hoge/.ssh/a_1.pem
```

これで準備完了だ。
まず、このコマンドを何も考えずに撃て。

```
$ EC2-Connect
0:  running  WebServiceApp  
1:  running  DatabaseApp  
3:  running  ScaleOutApp  
4:  stopped  TestServer  
```

どうだ。  
多少煩わしさが軽減出来ただろう。
