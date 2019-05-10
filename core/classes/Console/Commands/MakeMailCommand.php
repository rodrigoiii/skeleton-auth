<?php

namespace SkeletonMailApp\Console\Commands;

use SkeletonCore\BaseCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

class MakeMailCommand extends BaseCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    private $signature = "make:mail {mail}";

    /**
     * The command description.
     *
     * @var string
     */
    private $description = "Create mail class template.";

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct($this->signature, $this->description);
    }

    /**
     * To be call after execute the command.
     *
     * @param  Input $input
     * @param  Output $output
     * @return void
     */
    public function handle(Input $input, Output $output)
    {
        $mail = $input->getArgument('mail');

        try {
            if (!preg_match("/^[A-Z]\w*$/", $mail)) throw new \Exception("Error: Invalid mail name. It must be Characters and PascalCase.", 1);
            if (file_exists(app_path("SkeletonMail/{$mail}.php"))) throw new \Exception("Error: The mail name is already created.", 1);

            $is_created = $this->makeTemplate($mail);

            $output->writeln($is_created ? "Successfully created." : "File is not created.");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    /**
     * Create the mail template.
     *
     * @depends handle
     * @param  string $mail
     * @return boolean
     */
    private function makeTemplate($mail)
    {
        $file = __DIR__ . "/../templates/mail.php.dist";

        if (file_exists($file))
        {
            $template = strtr(file_get_contents($file), [
                '{{mail}}' => $mail
            ]);

            if (!file_exists(app_path("SkeletonMail")))
            {
                mkdir(app_path("SkeletonMail"), 0755, true);
            }

            $file_path = app_path("SkeletonMail/{$mail}.php");

            $file = fopen($file_path, "w");
            fwrite($file, $template);
            fclose($file);

            return file_exists($file_path);
        }

        return false;
    }
}
