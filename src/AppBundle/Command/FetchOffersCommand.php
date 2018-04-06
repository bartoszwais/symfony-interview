<?php 
// src/AppBundle/Command/FetchOffersCommand.php
namespace AppBundle\Command;

//use Symfony\Component\Console\Command\Command;
use AppBundle\Entity\Offer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class FetchOffersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        // the name of the command (the part after "bin/console")
        ->setName('app:fetch-offers')

        // the short description shown while running "php bin/console list"
        ->setDescription('Fetching offer data from Internet source.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to fetch data from Internet source...')
        ;

        $this
        // configure an argument
        ->addArgument('advertiserId', InputArgument::REQUIRED, 'The advertiser ID.')
        // ...
    ;
            
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $advertiserId = $input->getArgument('advertiserId');
        $output->writeln('Advertiser ID: '.$advertiserId);

        $doctrine = $this->getContainer()->get('doctrine');

        new FetchOfferXflirtJsonSource($doctrine, $advertiserId);
         
    }
}

class FetchOfferXflirtJsonSource {
    private $json_data;
    private $entriesNumber;

    function __construct($doctrine, $advertiserId) {

        $url = 'http://process.xflirt.com/advertiser/'.$advertiserId.'/offers';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        $data = json_decode($response);
        $this->json_data = $data;
        $this->save($doctrine);
    }

    public function getJsonData(){
        $newArray = array();

        for($i=1;$i<=$this->getEntriesNumber();$i++) {
            if(isset($this->json_data->{1}->payout_amount)){
                array_push($newArray,
                    [
                        $this->json_data->{$i}->countries[0],
                        $this->json_data->{$i}->payout_amount,
                        $this->json_data->{$i}->name,
                        $this->json_data->{$i}->mobile_platform]);
            } elseif(isset($this->json_data->{1}->campaigns->points)) {
                array_push($newArray, 
                    [
                        $this->json_data->{$i}->campaigns->countries[0],
                        $this->json_data->{$i}->campaigns->points*0.001,
                        $this->json_data->{$i}->app_details->developer,
                        $this->json_data->{$i}->app_details->platform
                    ]
                );
            }
        }
        return $newArray;
    }

    public function setUrl($advertiserId){
    }
    public function getEntriesNumber(){
        return count((array)$this->json_data);
    }

    private function save($doctrine){
        $em = $doctrine->getEntityManager();
        
        $array = $this->getJsonData();

        foreach ($array as $value) {
            if(!$doctrine->getRepository('AppBundle:Offer')->findOneBy(array('country' => $value[0], 'payout' => $value[1], 'name' => $value[2], 'platform' => $value[3]))) {
                $offer = new Offer();
                $offer->setCountry($value[0]);
                $offer->setPayout($value[1]);
                $offer->setName($value[2]);
                $offer->setPlatform($value[3]);

                $em->persist($offer);
                $em->flush();
            }
        } 
    }
}
