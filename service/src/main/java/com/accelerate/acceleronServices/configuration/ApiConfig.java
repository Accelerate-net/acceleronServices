package com.accelerate.acceleronServices.configuration;

import org.springframework.context.annotation.ComponentScan;
import org.springframework.context.annotation.Configuration;
import org.springframework.context.annotation.EnableAspectJAutoProxy;
import org.springframework.web.servlet.config.annotation.EnableWebMvc;
import org.springframework.web.servlet.config.annotation.WebMvcConfigurer;

@Configuration
@EnableWebMvc
@ComponentScan(basePackageClasses ={}, basePackages={"com.accelerate.acceleronServices.api"})
@EnableAspectJAutoProxy
public class ApiConfig implements WebMvcConfigurer {

}


