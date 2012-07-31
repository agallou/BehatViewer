<?php
namespace jubianchi\BehatViewerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputArgument;

class InstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('behat-viewer:install')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->format($output);
        $host = $this->apache2($output);
        $host = $this->mysql($output);




        $output->writeln(PHP_EOL . 'You should run the following commands for the configuration to take effect :');
        $output->writeln($this->getHelper('formatter')->formatBlock(
            array(
                'sudo a2ensite ' . $host,
                'sudo service apache2 restart'
            ),
            'info',
            true)
        );
    }

    private function format(OutputInterface $output)
    {
        $output->getFormatter()->setStyle('title', new \Symfony\Component\Console\Formatter\OutputFormatterStyle('white', 'blue', array('bold')));
    }

    private function title(OutputInterface $output, $title)
    {
        $title = 'Behat Viewer : ' . $title;
        $size = $this->getScreenSize();
        $width = $size->width - strlen($title);
        $width = $width < 0 ? 0 : $width;

        $str = str_repeat(' ', $width / 2);
        $output->writeln(
            $this->getHelper('formatter')->formatBlock(
                sprintf(
                    PHP_EOL . '%s%s%s' . PHP_EOL,
                    $str,
                    $title,
                    $str
                ),
                'title'
            )
        );
    }

    private function getScreenSize() {
        preg_match_all("/rows.([0-9]+);.columns.([0-9]+);/", strtolower(exec('stty -a |grep columns')), $matches);
        $size = new \StdClass;

        if(sizeof($matches) == 3) {
            $size->height = $matches[1][0];
            $size->width = $matches[2][0];
        } else {
            $size->width = 50;
            $size->height = 50;
        }

        return $size;
    }

    private function apache2(OutputInterface $output)
    {
        $this->title($output, 'Apache2 configuration');

        do {
            $host = $this->getHelper('dialog')->ask($output, 'Please enter the host you want to use for Behat Viewer <comment>[default: behat-viewer.local]</comment> ', 'behat-viewer.local');
            $admin = $this->getHelper('dialog')->ask($output, 'Please enter the server admin e-mail address <comment>[default: admin@' . $host . ']</comment> ', 'admin@' . $host);
            $docroot = realpath($this->getContainer()->get('kernel')->getRootDir() . '/../web');

            $vhost = $this->getVirtualHostTemplate($host, $docroot, $admin);

            $output->writeln('Here is the virtualhost configuration <comment>[/etc/apache2/sites-available/' . $host .']</comment>');
            $output->writeln(str_repeat('-', 50));
            $output->write($this->getHelper('formatter')->formatBlock($vhost, 'info'));
            $output->writeln("\r" . str_repeat('-', 50));

            $confirmation = $this->getHelper('dialog')->askConfirmation($output, 'Is this correct ? <comment>[Yes/No/Y/N/y/n][default: Yes]</comment> ');
        } while(false === $confirmation);

        if(is_writable('/etc/apache2/sites-available/' . $host)) {
            $file = new \SplFileObject('/etc/apache2/sites-available/' . $host, 'w+');
            $file->fwrite($vhost);
            $file->fflush();
        } else {
            $output->writeln(
                array(
                    '<error>The file was not written to Apache directory due to permissions problem.</error>',
                    '<error>Please, manually copy the configuration to /etc/apache2/sites-available/' . $host .'</error>',
                    PHP_EOL
                )
            );
        }

        return $host;
    }

    private function getVirtualHostTemplate($vhost, $docroot, $admin)
    {
        return <<<VHOST
<VirtualHost *:80>
    ServerName $vhost
    ServerAdmin $admin

    DocumentRoot $docroot

    <Directory $docroot>
        DirectoryIndex app.php
        Options -Indexes FollowSymLinks SymLinksifOwnerMatch
        AllowOverride All
        Allow from All
    </Directory>
</VirtualHost>

VHOST;
    }

    private function mysql(OutputInterface $output)
    {
        $this->title($output, 'MySQL configuration');

        $input = new \Symfony\Component\Console\Input\ArgvInput();
        $nullout = new \Symfony\Component\Console\Output\NullOutput();

        try {
            $this->getApplication()->find('doctrine:database:create')->run($input, $nullout);
        } catch(\Exception $exc) {
            $this->getApplication()->renderException($exc, $output);
        }

        try {
            $this->getApplication()->find('doctrine:schema:create')->run($input, $nullout);
        } catch(\Exception $exc) {
            $this->getApplication()->renderException($exc, $output);
        }

        $this->getApplication()->find('assets:install')->run(
            new \Symfony\Component\Console\Input\ArrayInput(array('--symlink', 'target' => 'web')),
            $output
        );
    }


}
