package com.accelerate.acceleronServices.smartMenu.dto.response;

import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;

@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class ApiResponse<T> {
	boolean status;
	int statusCode;
	String message;
	T data;
}
