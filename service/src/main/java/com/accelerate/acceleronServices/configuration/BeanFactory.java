package com.accelerate.acceleronServices.configuration;

import lombok.extern.slf4j.Slf4j;
import org.springframework.boot.autoconfigure.web.servlet.DispatcherServletRegistrationBean;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.web.context.support.AnnotationConfigWebApplicationContext;
import org.springframework.web.servlet.DispatcherServlet;

@Configuration
@Slf4j
public class BeanFactory {

    public static final String APP_ROOT = "/acceleron-services";

    @Bean
    public DispatcherServletRegistrationBean accountingApi() {
        DispatcherServlet dispatcherServlet = new DispatcherServlet();
        AnnotationConfigWebApplicationContext applicationContext = new AnnotationConfigWebApplicationContext();
        applicationContext.register(ApiConfig.class);
        dispatcherServlet.setApplicationContext(applicationContext);
        dispatcherServlet.setThrowExceptionIfNoHandlerFound(true);
        DispatcherServletRegistrationBean servletRegistrationBean = new DispatcherServletRegistrationBean(dispatcherServlet,
                APP_ROOT + "/u/*");
        servletRegistrationBean.setName("AcceleronApi");
        servletRegistrationBean.setLoadOnStartup(1);
        return servletRegistrationBean;
    }
}
