package com.accelerate.acceleronServices.reservation.dto.response;

import com.accelerate.acceleronServices.reservation.enums.StatusTextEnum;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.springframework.http.HttpStatus;

@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class ApiResponse<T> {
	boolean status;
	int statusCode;
	String message;
	T data;

	public ApiResponse<GenericResponse> badRequestResponse(String message){
		ApiResponse<GenericResponse> response = new ApiResponse<>();
		response.setStatus(false);
		response.setMessage(message);
		response.setStatusCode(HttpStatus.BAD_REQUEST.value());
		response.setData(new GenericResponse(StatusTextEnum.FAILURE.value()));
		return response;
	}

	public ApiResponse<GenericResponse> successResponse(String message){
		ApiResponse<GenericResponse> response = new ApiResponse<>();
		response.setStatus(true);
		response.setMessage(message);
		response.setStatusCode(HttpStatus.OK.value());
		response.setData(new GenericResponse(StatusTextEnum.SUCCESS.value()));
		return response;
	}
}
