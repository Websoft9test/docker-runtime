# Runtime

* 支持的环境参考：https://scalingo.com/runtimes
* 支持的数据库参考：https://scalingo.com/databases

## DNS

采用 [NginxProxyManager](https://github.com/NginxProxyManager/nginx-proxy-manager) 可视化管理工具

## 服务发现

```
docker run --name consul -d -p 8500:8500 consul
```

## 环境

1. 支持多种数据库
2. 支持多种环境
3. 统一组网
4. 支持单个容易部署多个应用

预设全部的应用编排文件，拉取镜像
