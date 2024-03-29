package com.accelerate.acceleronServices.ExceptionHandler;


import org.springframework.http.HttpStatus;

import java.time.LocalDateTime;

public class ErrorModel {

    private HttpStatus httpStatus;


    private String timestamp;

    private String message;

    private String details;

    public ErrorModel(HttpStatus httpStatus, String message, String details) {
        this.httpStatus = httpStatus;
        this.timestamp = LocalDateTime.now().toString();
        this.message = message;
        this.details = details;
    }

    public HttpStatus getHttpStatus() {
        return httpStatus;
    }


    public String getTimestamp() {
        return timestamp;
    }

    public String getMessage() {
        return message;
    }

    public String getDetails() {
        return details;
    }
}
