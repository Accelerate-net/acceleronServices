package com.accelerate.acceleronServices.dto.response;

import com.accelerate.acceleronServices.enums.StatusCodes;

// Generic response for an API call
public class ApiResponse {

	protected boolean status;
	protected int statusCode;
	protected String message;
	
	public ApiResponse(boolean status, StatusCodes statusCode, String message) {
		this.status = status;
		this.statusCode = statusCode.value();
		this.message = message;
	}

	public boolean isStatus() {
		return status;
	}

	public void setStatus(boolean status) {
		this.status = status;
	}

	public int getStatusCode() {
		return statusCode;
	}

	public void setStatusCode(int statusCode) {
		this.statusCode = statusCode;
	}

	public String getMessage() {
		return message;
	}

	public void setMessage(String message) {
		this.message = message;
	}
}
