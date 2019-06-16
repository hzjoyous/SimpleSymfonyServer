<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class DemoCommand extends Command
{
    protected static $defaultName = 'z:Demo';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = "/mnt/d/workSpace/hzj/hexo/source/_posts";
        $io      = new SymfonyStyle($input, $output);
        $arg1    = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($option1 = $input->getOption('option1')) {
            $io->note($option1);
        }

        $io->title('这是使用 title 输出的');


        $io->comment('这是使用 comment 输出的');

        $io->caution('这是使用 caution 输出的');

        $io->block('这是 block 输出的');

        $io->writeln('这是使用 writeln 输出的');

        $io->text('这是 text 输出的');

        $io->newLine(5);

        $raws = [
            ['BiuBiuBiu', '30'],
            ['BiuBiuBiu', '30'],
            ['BiuBiuBiu', '30'],
            ['BiuBiuBiuBoomBoomBoom', '30'],
        ];

        $io->table(['name', 'age'], $raws);

        $io->error('这是 error');

        $io->warning('这是 waring');

        $io->success('这是 success');


        $confirmResult = $io->confirm('这是 confirm question ');

        $io->writeln($confirmResult);

        $askQuestionResult = $io->ask('What is your name?');

        dump($askQuestionResult);

        $name = $io->askQuestion(new Question('What is your name?'));

        dump($name);


        if ($io->confirm('Do you wish to continue?')) {
            $io->writeln('no');
        }

        $name = $io->choice('What is your name?', ['Taylor', 'Dayle'], 'Dayle');

        $io->writeln($name);

        $i           = 0;
        $progressMax = 100;
        $bar         = $io->createProgressBar($progressMax);
        while ($i++ < $progressMax) {
            usleep(300);
            $bar->advance();
        }

        $bar->finish();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
