<a name="readme-top"></a>
<!-- PROJECT SHIELDS -->
<!--
*** I'm using markdown "reference style" links for readability.
*** Reference links are enclosed in brackets [ ] instead of parentheses ( ).
*** See the bottom of this document for the declaration of the reference variables
*** for contributors-url, forks-url, etc. This is an optional, concise syntax you may use.
*** https://www.markdownguide.org/basic-syntax/#reference-style-links
-->

[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]

<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/DevOlegKosarev/CronJobCommand">
    <img src="https://raw.githubusercontent.com/DevOlegKosarev/DevOlegKosarev/main/images/logo.png" alt="project_title" width="80" height="80">
  </a>

  <h3 align="center">CronJobCommand</h3>
  <p align="center">
    <a href="https://github.com/DevOlegKosarev/CronJobCommand"><strong>Explore the docs »</strong></a>
    ·
    <a href="https://github.com/DevOlegKosarev/CronJobCommand/issues">Report Bug</a>
    ·
    <a href="https://github.com/DevOlegKosarev/CronJobCommand/issues">Request Feature</a>
  </p>
</div>

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgments">Acknowledgments</a></li>
  </ol>
</details>

<!-- ABOUT THE PROJECT -->

## About The Project

[![CronJobCommand Screen Shot][product-screenshot]](https://example.com)

CronJobCommand is a custom command for CodeIgniter 4 that allows you to run scheduled tasks based on the task group name.
___
`DevOlegKosarev`, `CronJobCommand`, `CodeIgniter`, `CodeIgniter 4`, `spark`, `php`, `cron`

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Built With

[![PHP][php.net]][php-url] [![CodeIgniter][codeigniter.com]][codeigniter-url] [![MySQL][mysql.com]][mysql-url] 

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- GETTING STARTED -->

## Getting Started

This is an example of how you may give instructions on setting up your project locally.
To get a local copy up and running follow these simple example steps.

### Prerequisites

This is an example of how to list things you need to use the software and how to install them.

### Installation

To use this command, you need to copy the `CronJobCommand.php` file to the `app/Commands` folder of your project. You also need to have a `CronJobs.php` migration file in the `app/Database/Migrations` folder of your project. If you don't have it, you can copy it from the project repository. This migration creates the tasks table in your database. You can only run this migration with the command:

```sh
php spark migrate:file "app\Database\Migrations\CronJobs.php"
```

This command will only migrate `CronJobs`.
If you want to add demo data to the tasks table, you can use the `--demo` option when running the migration. For example:

```sh
php spark migrate:file "app\Database\Migrations\CronJobs.php" --demo
```
This command will migrate `CronJobs` and insert some sample jobs into the tasks table.
To work with the tasks table, you also need to have a `CronJobModel` model in the `app/Models` folder of your project. This model allows you to interact with task data through an object-oriented interface. You can copy this model from the project repository or create it yourself.

In the tasks table, you can add, edit, or delete scheduled tasks. Each task has the following fields:
  - task_id: unique task ID
  - task_name: task name
  - task_group: task group name
  - task_command: command to be executed for the task
  - task_schedule: task execution schedule in cron format
  - task_status: task status (enabled or disabled)
  - task_last_run: date and time of the last task execution

<!-- USAGE EXAMPLES -->

## Usage

To run scheduled tasks, you need to call the `cron:job` command with the `task_group` argument, which specifies the name of the group of tasks to be run. For example:

```sh
php spark cron:job email
```
This command will run all enabled tasks from the email group on a schedule.
You can add this command to your server's crontab so that it runs automatically at a certain frequency. For example, you can add the following line to crontab:

```sh
* * * * * php /path/to/your/project/spark cron:job email
```
This line means that the command will run every minute and perform tasks from the email group.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- ROADMAP -->

## Roadmap

- [x] CodeIgniter 4.x
- [ ] CodeIgniter 3.x
- [ ] CodeIgniter 2.x
- [ ] CodeIgniter 1.x
  
See the [open issues](https://github.com/DevOlegKosarev/CronJobCommand/issues) for a full list of proposed features (and known issues).

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- CONTRIBUTING -->

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- LICENSE -->

## License

Distributed under the MIT License. See `LICENSE.txt` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- CONTACT -->

## Contact

Oleg Kosarev - dev.oleg.kosarev.@outlook.com

Project Link: [https://github.com/DevOlegKosarev/CronJobCommand](https://github.com/DevOlegKosarev/CronJobCommand)

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->

[stars-shield]: https://img.shields.io/github/stars/DevOlegKosarev/CronJobCommand.svg?style=for-the-badge
[stars-url]: https://github.com/DevOlegKosarev/CronJobCommand/stargazers
[issues-shield]: https://img.shields.io/github/issues/DevOlegKosarev/CronJobCommand.svg?style=for-the-badge
[issues-url]: https://github.com/DevOlegKosarev/CronJobCommand/issues
[license-shield]: https://img.shields.io/github/license/DevOlegKosarev/CronJobCommand.svg?style=for-the-badge
[license-url]: https://github.com/DevOlegKosarev/CronJobCommand/blob/master/LICENSE.txt
[product-screenshot]: https://raw.githubusercontent.com/DevOlegKosarev/DevOlegKosarev/main/images/screenshot/parsing.png


[php.net]: https://img.shields.io/badge/php-484C89?style=for-the-badge&logo=php&logoColor=white
[php-url]: https://php.net

[codeigniter.com]: https://img.shields.io/badge/codeigniter-dd4814?style=for-the-badge&logo=codeigniter&logoColor=white
[codeigniter-url]: https://codeigniter.com

[mysql.com]: https://img.shields.io/badge/MySQL-00758F?style=for-the-badge&logo=MySQL&logoColor=white
[mysql-url]: https://codeigniter.com
