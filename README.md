# Wechat Work Notifier
[Kanboard](https://github.com/kanboard/kanboard)通知插件，帮助您通过企业微信接收通知。

A notification plugin for [Kanboard](https://github.com/kanboard/kanboard), which helps you receive notifications on Wechat Work, aka Wecom.

## Features
用户通知
- 在被@后通知我。 Notifying me after being mentioned.

项目通知
- 创建任务后，发送通知给任务成员。 Notifying task members after creation.
- 发送通知给被指派的项目成员。 Notifying someone who has been assigned.
- 在任务移动后，发送通知给任务成员。 Notifying members after moving a task.
- 在任务变更后，发送通知给任务成员。 Notifying members after changing a task.
- 在评论更新后，发送通知给任务成员。 Notifying members after updating comments.

其他
- 支持Group_Assign插件。 Provide support for the plugin Group_Assign.

## Getting started
1. 确保您拥有一个企业微信中的应用的管理权限。Assure yourself of an application's management authority in Wechat Work.

2. 通过Kanboard插件管理界面安装，或者克隆本仓库至插件目录。Install from the Kanboard plugin manager directly, or clone this repository to your plugin directory.

3. 复制并重命名文件 `config-default.php` 为 `config.php`，然后根据注释中的说明进行编辑。Copy and rename the file `config-default.php` to `config.php`, then edit it by following the instructions in the comments.

## Caution
⚠️

请确保Kanboard用户的邮件地址和他们在企业微信中邮件地址相同。

Ensure that the email addresses of the users in your Kanboard are exactly the same as those on their Wechat Work. 

## Author
Greyaz

## License
License MIT
