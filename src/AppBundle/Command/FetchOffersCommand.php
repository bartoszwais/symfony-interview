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
        $output->writeln('Advertiser ID: '.$input->getArgument('advertiserId'));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://process.xflirt.com/advertiser/'.$input->getArgument('advertiserId').'/offers');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        $data = json_decode($response);
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();

        for($i=1;$i<=count((array)$data);$i++) {
            if(isset($data->{1}->payout_amount)){
                if(!$doctrine->getRepository('AppBundle:Offer')->findOneBy(array('country' => $data->{$i}->countries[0], 'payout' => $data->{$i}->payout_amount, 'name' => $data->{$i}->name, 'platform' => $data->{$i}->mobile_platform))) {

                    $offer = new Offer();
                    $offer->setCountry($data->{$i}->countries[0]);
                    $offer->setPayout($data->{$i}->payout_amount);
                    $offer->setName($data->{$i}->name);
                    $offer->setPlatform($data->{$i}->mobile_platform);
                    $em->persist($offer);
                    $em->flush();
                }
            } elseif(isset($data->{1}->campaigns->points)) {
                if(!$doctrine->getRepository('AppBundle:Offer')->findOneBy(array('country' => $data->{$i}->campaigns->countries[0], 'payout' => $data->{$i}->campaigns->points*0.001, 'name' => $data->{$i}->app_details->developer, 'platform' => $data->{$i}->app_details->platform))) {
                    $offer = new Offer();
                    $offer->setCountry($data->{$i}->campaigns->countries[0]);
                    $offer->setPayout($data->{$i}->campaigns->points*0.001);
                    $offer->setName($data->{$i}->app_details->developer);
                    $offer->setPlatform($data->{$i}->app_details->platform);
                    $em->persist($offer);
                    $em->flush();
                }
            } 
        }
    }
}
