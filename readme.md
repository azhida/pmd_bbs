## 坡马代论坛

## 部署说明

1、cp .env.example .env，配置相关信息

2、给 storage 目录分配读写权限

## 注意事项
生产环境下使用队列需要注意以下两个问题：
- 1、使用 Supervisor 进程工具进行管理，配置和使用请参照 文档(https://learnku.com/docs/laravel/5.5/horizon/1345#Supervisor-%E9%85%8D%E7%BD%AE) 进行配置
- 2、每一次部署代码时，需 artisan horizon:terminate 然后再 artisan horizon 重新加载代码