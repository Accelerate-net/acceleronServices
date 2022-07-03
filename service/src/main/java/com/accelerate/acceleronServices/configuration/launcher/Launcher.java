package com.accelerate.acceleronServices.configuration.launcher;

import lombok.extern.slf4j.Slf4j;
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.EnableAutoConfiguration;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.boot.autoconfigure.data.mongo.MongoDataAutoConfiguration;
import org.springframework.boot.autoconfigure.domain.EntityScan;
import org.springframework.boot.autoconfigure.jdbc.DataSourceAutoConfiguration;
import org.springframework.boot.autoconfigure.jdbc.DataSourceTransactionManagerAutoConfiguration;
import org.springframework.boot.autoconfigure.mongo.MongoAutoConfiguration;
import org.springframework.boot.autoconfigure.orm.jpa.HibernateJpaAutoConfiguration;
import org.springframework.boot.autoconfigure.web.servlet.DispatcherServletAutoConfiguration;
import org.springframework.boot.autoconfigure.web.servlet.WebMvcAutoConfiguration;
import org.springframework.boot.builder.SpringApplicationBuilder;
import org.springframework.boot.web.servlet.support.SpringBootServletInitializer;
import org.springframework.cache.annotation.EnableCaching;
import org.springframework.context.annotation.ComponentScan;
import org.springframework.context.annotation.Configuration;
import org.springframework.context.annotation.FilterType;
import org.springframework.context.annotation.Import;
import org.springframework.data.jpa.repository.config.EnableJpaRepositories;
import org.springframework.scheduling.annotation.EnableAsync;

//@EntityScan("com.accelerate.acceleronServices.model")
//@ComponentScan("com.accelerate.acceleronServices.repository")
//@EnableJpaRepositories("")



@ComponentScan(basePackages = {"com.accelerate"}, excludeFilters = {@ComponentScan.Filter(type = FilterType.REGEX, pattern = "com\\.accelerate\\..*launcher\\..*")})
//@EnableAutoConfiguration(exclude = {
//		MongoAutoConfiguration.class,
//		MongoDataAutoConfiguration.class,
//		DataSourceAutoConfiguration.class,
//		DataSourceTransactionManagerAutoConfiguration.class,
//		HibernateJpaAutoConfiguration.class,
//		DispatcherServletAutoConfiguration.class,
//		WebMvcAutoConfiguration.class
//})
@EnableAutoConfiguration(exclude = {
		DispatcherServletAutoConfiguration.class
})
@Configuration
@Slf4j
@EnableAsync
@EnableCaching
@EntityScan(basePackages= {"com.accelerate.acceleronServices.reservation.model","com.accelerate.acceleronServices.smartMenu.model"})
@EnableJpaRepositories(basePackages = {"com.accelerate.acceleronServices.reservation.repository","com.accelerate.acceleronServices.smartMenu.repository"})
public class Launcher extends SpringBootServletInitializer {

	public static void main(String[] args) {
		SpringApplication.run(Launcher.class, args);
	}

	@Override
	protected SpringApplicationBuilder configure(SpringApplicationBuilder application) {
		return application.sources(Launcher.class);
	}

}
