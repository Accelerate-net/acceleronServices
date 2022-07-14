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

}
