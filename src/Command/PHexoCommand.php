<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class PHexoCommand extends Command
{
    protected static $defaultName = 'z:pHexo';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');
        $f = new Finder();


//        $postPath = __DIR__ . '/../../' . $arg1;
//        $postDir = realpath($postPath);
        $postDir = '/mnt/d/WorkSpace/hzj/hexo/source/_posts';
        dump($postDir);

        $d = $f->files()->in($postDir);


        $postList = [];
        $number = 1;

        foreach ($d as $file) {
            /* @param SplFileInfo $file */
            $content = $file->getContents();

            preg_match_all('#---(.*?)---{1}([\s\S]*)$#ms',
                $content,
                $out, PREG_PATTERN_ORDER);

            $header = $out[1][0] ?? null;
            $content = $out[2][0] ?? null;

//            if (!strstr($header, '[020')) {
//                continue;
//            }
//            dump($out);

            if (is_string($header)) {
                $q = Yaml::parse($header);
                $q['title'] = $q['title'] ?? '';
                $q['date'] = $q['date'] ?? '';
                $q['tags'] = $q['tags'] ?? '';
                $q['categories'] = $q['categories'] ?? '';
                $q['p'] = $q['p'] ?? '';

            } else {
                $io->error('header缺失');
            }
            if (!is_string($content)) {
                $io->warning('header缺失');
            }
            $q['number'] = $number;
            $number += 1;
            $postList[] = array_map(function ($item) {
                return is_array($item) ? implode(',', $item) : $item;
            }, $q);

        }

        $io->table(['title', 'p', 'date', 'tags', 'categories', 'number'], $postList);
        $title = '';

        $date = '';
        $tags = '';
        $categories = '';

        $content = '';


        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
