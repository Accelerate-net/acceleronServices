package com.accelerate.acceleronServices.dto.response;

import com.accelerate.acceleronServices.enums.StatusCodes;

public class SuccessResponse extends ApiResponse {
    public SuccessResponse(boolean status, StatusCodes statusCode, String message) {
        super(status, statusCode, message);
    }
}
