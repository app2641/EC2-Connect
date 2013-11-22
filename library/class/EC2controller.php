<?php

require_once dirname(__FILE__).'/../composer/vendor/autoload.php';


use Aws\Ec2\Ec2Client,
    Aws\Common\Enum\Region;

class EC2controller
{
    /**
     *  EC2Clientクラス
     **/
    protected $ec2;


    /**
     * sshキーパス
     **/
    protected $ssh_key;


    public function __construct ()
    {
        $aws_ini_path = ROOT.'/config/aws.ini';
        if (! file_exists($aws_ini_path)) {
            throw new \Exception('AWS設定ファイル aws.ini を生成してください');
        }

        $ini = parse_ini_file($aws_ini_path);
        $this->ssh_key = $ini['sshkey'];

        $this->ec2 = Ec2Client::factory(
            array(
                'key' => $ini['key'],
                'secret' => $ini['secret'],
                'region' => Region::TOKYO
            )
        );
    }



    /**
     * EC2インスタンスのリストを表示する
     *
     * @return void
     **/
    public function getInstanceList ()
    {
        $response  = $this->ec2->describeInstances();
        $instances = $response->getAll(array('Reservations'));
        $data      = array();
        $status    = array();

        // Nameを取得する
        foreach ($instances['Reservations'] as $instance) {
            foreach ($instance['Instances'][0]['Tags'] as $tag) {
                if ($tag['Key'] == 'Name') {
                    $data[] = $tag['Value'];
                }
            }

            $status[] = $instance['Instances'][0]['State']['Name'];
        }


        // ターミナルに表示
        foreach ($data as $num => $val) {
            echo $num.':  '.$status[$num].'  '.$val.PHP_EOL;
        }
    }




    /**
     * 指定した番号のEC2インスタンスに接続する
     *
     * @param int $number  指定インスタンス番号
     * @return void
     **/
    public function connect ($number)
    {
        $response  = $this->ec2->describeInstances();
        $instances = $response->getAll(array('Reservations'));

        foreach ($instances['Reservations'] as $i => $instance) {
            if ($i == $number) {
                if ($instance['Instances'][0]['State']['Name'] != 'running') {
                    echo 'ステータスが running ではありません'.PHP_EOL;
                    break;
                }

                $dns_name = $instance['Instances'][0]['PublicDnsName'];
                $command = 'ssh ec2-user@'.$dns_name.' -i '.$this->ssh_key;
                touch ('/tmp/EC2-Connect.query');
                file_put_contents('/tmp/EC2-Connect.query', $command);
                exec('cat /tmp/EC2-Connect.query | pbcopy');
                break;
            }
        }
    }
}
