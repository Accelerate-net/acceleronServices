package com.accelerate.acceleronServices.configuration;

import com.accelerate.acceleronServices.reservation.utils.CreateSummary;
import com.accelerate.acceleronServices.reservation.utils.EntityDtoConversion;
import lombok.extern.slf4j.Slf4j;
import org.modelmapper.ModelMapper;
import org.springframework.boot.autoconfigure.web.servlet.DispatcherServletRegistrationBean;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.scheduling.concurrent.ThreadPoolTaskExecutor;
import org.springframework.web.client.RestTemplate;
import org.springframework.web.context.support.AnnotationConfigWebApplicationContext;
import org.springframework.web.servlet.DispatcherServlet;

@Configuration
@Slf4j
public class CommonBeanFactory {

    public static final String APP_ROOT = "/acceleron-services";

    @Bean
    public DispatcherServletRegistrationBean acceleronApi() {
        DispatcherServlet dispatcherServlet = new DispatcherServlet();
        AnnotationConfigWebApplicationContext applicationContext = new AnnotationConfigWebApplicationContext();
        applicationContext.register(ApiConfig.class);
        dispatcherServlet.setApplicationContext(applicationContext);
        dispatcherServlet.setThrowExceptionIfNoHandlerFound(true);
        DispatcherServletRegistrationBean servletRegistrationBean = new DispatcherServletRegistrationBean(dispatcherServlet, APP_ROOT + "/api/*");
        servletRegistrationBean.setName("AcceleronApi");
        servletRegistrationBean.setLoadOnStartup(1);
        return servletRegistrationBean;
    }

    @Bean
    public DispatcherServlet dispatcherServlet() {
        return new DispatcherServlet();
    }

    @Bean
    public ThreadPoolTaskExecutor threadPoolTaskExecutor() {

        ThreadPoolTaskExecutor executor = new ThreadPoolTaskExecutor();
        executor.setCorePoolSize(16);
        executor.setMaxPoolSize(32);
        executor.setQueueCapacity(1000);
        executor.setThreadNamePrefix("default_task_executor_thread");
        executor.initialize();

        return executor;
    }

    @Bean
    public RestTemplate restTemplate() {
        return new RestTemplate();
    }

    @Bean
    public ModelMapper modelMapper() {
        return new ModelMapper();
    }


    @Bean
    public EntityDtoConversion entityDtoConversion(){
        return new EntityDtoConversion();
    }

    @Bean
    public CreateSummary createSummary(){
        return new CreateSummary();
    }
}
