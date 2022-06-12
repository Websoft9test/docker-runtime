# Runtime

* 支持的环境参考：https://scalingo.com/runtimes
* 支持的数据库参考：https://scalingo.com/databases

市场分析：https://www.jetbrains.com/lp/devecosystem-2021
搜索关键词 php  Benchmarks 

## DNS

采用 [NginxProxyManager](https://github.com/NginxProxyManager/nginx-proxy-manager) 可视化管理工具

## 服务发现

```
docker run --name consul -d -p 8500:8500 consul
```

## 环境

1. 支持多种数据库
2. 支持多版本
4. 支持多框架
3. 统一组网
4. 支持单个容易部署多个应用

预设全部的应用编排文件，拉取镜像

### PHP

缺乏 composer, git, unzip ,mysql 驱动等

市场分析：
* https://www.jetbrains.com/lp/devecosystem-2021/php/
* https://kinsta.com/blog/php-benchmarks/

### Java

Java 万能安装工具包：https://sdkman.io/，安装多个 SDK 版本，也可以安装 java 生态中的各种工具。 
