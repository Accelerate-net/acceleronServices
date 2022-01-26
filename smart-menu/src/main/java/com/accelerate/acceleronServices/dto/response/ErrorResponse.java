package com.accelerate.acceleronServices.dto.response;

import com.accelerate.acceleronServices.enums.StatusCodes;

// Generic response for an error
public class ErrorResponse extends ApiResponse {

    public ErrorResponse(boolean status, StatusCodes statusCode, String message) {
        super(status, statusCode, message);
    }
}
